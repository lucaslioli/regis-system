<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="author" content="Lucas Lima" />
        <!-- 
            Based in https://cwiki.apache.org/confluence/display/solr/SolJSON#SolJSON-UsingSolr%E2%80%99sJSONoutputforAJAX

            In case of CORS console error, install and activate a CORS extension, such as
            https://chrome.google.com/webstore/detail/moesif-origin-cors-change/digfbfaphojjndkpccljibejjbppifbc?utm_source=chrome-ntp-icon
        -->

        <title>REGIS Collection - Basic search</title>

        <link rel="shortcut icon" href="https://geodigitalregis.inf.ufrgs.br/images/regis-favicon.png" />

        <link href="{{ asset('css/app.css') }}" rel="stylesheet">
        <link href="{{ asset('css/layout.css') }}" rel="stylesheet">
        <link href="{{ asset('css/basicSearch.css') }}" rel="stylesheet">

        <script src="{{ asset('js/jquery-3.5.1.min.js') }}"></script>
        <script src="{{ asset('js/scripts.js') }}"></script>
        
        <script language="Javascript">
            // derived from http://www.degraeve.com/reference/simple-ajax-example.php
            function xmlhttpPost() {
                document.getElementById("status").innerHTML = "&nbsp;Searching <i class='fas fa-spinner fa-spin'></i>";

                // var strURL = "http://localhost:8080/solr/boletins/select";
                var strURL = "https://geodigitalregis.inf.ufrgs.br/solr/regis-collection/select";
                var xmlHttpReq = false;
                var self = this;
                if (window.XMLHttpRequest) { // Mozilla/Safari
                    self.xmlHttpReq = new XMLHttpRequest();
                }
                else if (window.ActiveXObject) { // IE
                    self.xmlHttpReq = new ActiveXObject("Microsoft.XMLHTTP");
                }

                self.xmlHttpReq.open('POST', strURL, true);
                self.xmlHttpReq.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                self.xmlHttpReq.onreadystatechange = function() {
                    if (self.xmlHttpReq.readyState == 4) {
                        updatepage(self.xmlHttpReq.responseText);
                    }
                }

                var params = getstandardargs();
                var strData = params.join('&');
                self.xmlHttpReq.send(strData);
            }

            function getstandardargs() {
                var form = document.forms['f1'];

                var query = form.query.value;
                var numdocs = (form.numdocs.value)?form.numdocs.value:5;
                var filter = (form.filter.value)?form.filter.value:'*';

                // Remove special characters
                query = query.normalize('NFD').replace(/[\u0300-\u036f]/g, "");
                query = escape(query);

                if(form.proximity.checked)
                    query = '("'+query.replace(/"/g, '')+'"~10 '+query+')';
                else
                    query = query;

                var params = [
                    'q='+query,
                    'fq=docid:'+filter,
                    'wt=json',
                    'indent=on',
                    'fl=*, score',
                    'rows='+numdocs,
                    'df=text'
                ];

                return params;
            }

            // this function does all the work of parsing the solr response and updating the page.
            function updatepage(str){
                responseHeader = str.substr(0, str.indexOf('"numFound"'))+" ... } \n}";
                document.getElementById("raw").innerHTML = responseHeader;

                var rsp = eval("("+str+")"); // use eval to parse Solr's JSON response
                var html = "<hr><b>Documents found:</b> " + rsp.response.numFound + "<hr>";

                rsp.response.docs.forEach(element => {
                    element.filename = element.filename[0].replace(/%20/g, '_');
                    element.filename = element.filename.replace(/ /g, '_');
                    element.filename = element.filename.replace(/%/g, '');

                    element.text = highlightText(element.text);
                    docNum = element.docid.toString().substr(6);

                    html += "<div class='doc'>";

                    html += "<b>Doc ID</b>: "+element.docid;
                    html += "<button class='ml-3' onclick=\"copyToClipboard('"+element.docid+"')\">Copy ID <i class='far fa-copy'></i></button>";
                    html += "<br><b>File name</b>: "+
                        "<a href='https://geodigitalregis.inf.ufrgs.br/documents/"+element.filename+"' target='blank'>"+
                            element.filename+" <i class='fas fa-external-link-alt'></i></a>";
                    html += "<br><b>File type</b>: "+element.filetype;
                    html += "<br><b>Content</b>: <div class='float-right'>"+
                        "<button onclick='findFirstLast(\""+docNum+"\", 1)'><i class='fas fa-angle-double-left'></i> First</button>&nbsp;"+
                        "<button onclick='findPrevMarker(\""+docNum+"\")' id='btnPrev-"+docNum+"'><i class='fas fa-angle-left'></i> Previous</button>&nbsp;"+
                        "<button onclick='findNextMarker(\""+docNum+"\")' id='btnNext-"+docNum+"'>Next <i class='fas fa-angle-right'></i></button>&nbsp;"+
                        "<button onclick='findFirstLast(\""+docNum+"\", 0)'>Last <i class='fas fa-angle-double-right'></i></button>"+
                        "</div><div class='card'>"+
                            "<div class='card-body document-text' id='content-"+docNum+"'><pre>"+
                                element.text+
                            "</pre></div>"+
                        "</div><br>";

                    html += "</div>";
                });

                document.getElementById("result").innerHTML = html;
                document.getElementById("status").innerHTML = "";
            }

            function highlightText(text){
                var words = document.getElementById('query').value;
                words = words.replace(/[(){}&|!*^"'~?/:\[\]\+\-]/g, '');
                words = words.split(" ");

                for (i = 0; i < words.length; i++){
                    if(words[i] == "" || words[i] == " ")
                        continue;
                    
                    var re = new RegExp(words[i], 'ig');
                    text = text.replace(re, '<mark>'+words[i]+'</mark>');

                    wordNorm = words[i].normalize('NFD').replace(/[\u0300-\u036f]/g, "");

                    if(words[i] != wordNorm){
                        re = new RegExp(wordNorm, 'ig');
                        text = text.replace(re, '<mark>'+wordNorm+'</mark>');
                    }
                }

                return text;
            }

            /** 
             * Copies a string to the clipboard. Must be called from within an
             * event handler such as click. May return false if it failed, but
             * this is not always possible. Browser support for Chrome 43+,
             * Firefox 42+, Safari 10+, Edge and Internet Explorer 10+.
             * Internet Explorer: The clipboard feature may be disabled by
             * an administrator. By default a prompt is shown the first
             * time the clipboard is used (per session).
             */
            function copyToClipboard(text) {
                if (window.clipboardData && window.clipboardData.setData) {
                    // Internet Explorer-specific code path to prevent textarea being shown while dialog is visible.
                    return clipboardData.setData("Text", text);

                } else if (document.queryCommandSupported && document.queryCommandSupported("copy")) {
                    var textarea = document.createElement("textarea");
                    textarea.textContent = text;
                    textarea.style.position = "fixed";  // Prevent scrolling to bottom of page in Microsoft Edge.
                    document.body.appendChild(textarea);
                    textarea.select();
                    try {
                        return document.execCommand("copy");  // Security exception may be thrown by some browsers.
                    } catch (ex) {
                        console.warn("Copy to clipboard failed.", ex);
                        return false;
                    } finally {
                        document.body.removeChild(textarea);
                    }
                }
            }
        </script>
    </head>

    <body>
        <div class="container mt-5">
            <pre class="d-flex justify-content-between">
                <div><b>Searching into REGIS Collection</b><br>Total of 21656 PDF documents<br><br>{{--
                --}}<a href="{{ url()->previous()!=route('basic_seach') ? url()->previous() : route('welcome') }}">{{--
                    --}}<i class='fas fa-arrow-left'></i> Return to system</a>
                </div>

                <div>                    <b>Apache Solr 8.6.3 Config</b>
                    Similarity function BM25Similarity
                    LowerCaseFilterFactory
                    StopFilterFactory
                    PortugueseLightStemFilterFactory
                    Performs proximity search, with 10 words
                </div>
            </pre>

            <form name="f1" onsubmit='xmlhttpPost(); return false;'>
                <div class="d-flex align-items-end">
                    
                    <div>
                        Query: 
                        <input name="query" id="query" type="text" class="form-control" size="40">
                    </div>

                    <div class="ml-2">
                        Documents: 
                        <input name="numdocs" id="numdocs" type="number" value=10 min=1 max=100 class="form-control">
                    </div>

                    <div class="ml-2">
                        Filter: 
                        <select name="filter" id="filter" title="Filter returned documents" class="form-control">
                            <option value="*">Select...</option>
                            <option value="BG||BP||BT">BR-B - Boletins Petrobras</option>
                            <option value="TU">BR-T - Teses e Dissertações</option>
                        </select>
                    </div>

                    <div class="custom-control custom-checkbox ml-2">
                        <input type="checkbox" id="proximity" class="custom-control-input mt-2">
                        <label class="custom-control-label mb-1" for="proximity">Proximity Search</label>
                    </div>

                    <button type="submit" value="Search" class="btn btn-primary ml-2 pl-4 pr-4">
                        <i class='fas fa-search'></i> Search
                    </button>
                    <span id="status" class="ml-3"></span>
                </div>

                <div class="text-muted mt-3">
                    You can use 
                    <strong>&&</strong> to requires that both terms to be present,
                    <strong>||</strong> to requires that one of the terms to be present, 
                    <strong>!</strong> to require the term not be present.
                    <br>You also can use <strong>?</strong> to match a single character, and
                    <strong>*</strong> to match zero or more sequential.
                    PS.: Query without accentuation may not be highlighted in text.
                </div>
                <div class="text-muted">
                    For more help in formulate the queries, take a look at the
                    <a href="https://lucene.apache.org/solr/guide/8_6/the-standard-query-parser.html#boolean-operators-supported-by-the-standard-query-parser"
                        target="blank">documentation</a>.
                </div>
            </form>

            <div id="result" class="mt-2"></div>

            <hr>

            <pre><b>Raw JSON response header: </b>
                <div class="card">
                    <div id="raw" class="card-body"></div>
                </div>
            </pre>

        </div>
    </body>
</html>

