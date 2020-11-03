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

        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css"
            integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">

        <style>
            #raw {
                max-height: 600px;
                overflow: auto;
            }
            .content{
                max-height: 300px;
                overflow: auto;
                overflow-x: scroll;
            }
            .content pre {
                overflow: initial;
            }
        </style>
        
        <script language="Javascript">
            // derived from http://www.degraeve.com/reference/simple-ajax-example.php
            function xmlhttpPost() {
                document.getElementById("status").innerHTML = "&nbsp;Searching...";

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
                // 
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

            function getstandardargs(query) {
                var form = document.forms['f1'];

                var query = escape(form.query.value);
                var numdocs = (form.numdocs.value)?form.numdocs.value:5;

                var params = [
                    'q=("'+query+'"~10 '+query+')',
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
                document.getElementById("raw").innerHTML = str;

                var rsp = eval("("+str+")"); // use eval to parse Solr's JSON response
                var html= "<hr><b>Documents found:</b> " + rsp.response.numFound+"<hr>";

                rsp.response.docs.forEach(element => {
                    html += "<div class='doc'>";

                    html += "<b>Doc ID</b>: "+element.docid;
                    html += "<br><b>File name</b>: "+
                        "<a href='https://geodigitalregis.inf.ufrgs.br/documents/"+element.filename+"' target='blank'>"+
                            element.filename+"</a>";
                    html += "<br><b>File type</b>: "+element.filetype;
                    html += "<br><b>Content</b>: <div class='card'>"+
                        "<div class='card-body content'><pre>"+
                            element.text+
                        "</pre></div></div><br>";

                    html += "</div>";
                });

                document.getElementById("result").innerHTML = html;
                document.getElementById("status").innerHTML = "";
            }
        </script>
    </head>

    <body>
        <div class="container mt-5">
            <pre class="d-flex justify-content-between">
                <div><b>Searching into REGIS Collection</b><br>Total of 21656 PDF documents</div>

                <div>                    <b>Apache Solr 8.6.3 Config</b>
                    Similarity function BM25Similarity
                    LowerCaseFilterFactory
                    StopFilterFactory
                    PortugueseLightStemFilterFactory
                    Performs proximity search, with 10 words
                </div>
            </pre>

            <form name="f1" onsubmit='xmlhttpPost(); return false;'>
                <p>
                    Query: <input name="query" type="text"> &nbsp;
                    Documents: <input name="numdocs" type="number" value=5 min=1 max=100>

                    <input value="Go" type="submit">
                    <span id="status"></span>
                </p>
                <div id="result" class="mt-2"></div>

                <hr>
                
            </form>

            <pre><b>Raw JSON String: </b>
                <div class="card">
                    <div id="raw" class="card-body"></div>
                </div>
            </pre>

        </div>
    </body>
</html>