<?php

use Illuminate\Support\Facades\Auth;

if(!function_exists('removeStopWords'))
{
    function removeStopWords($text)
    {
        $stop_words = array('de', 'a', 'o', 'que', 'e', 'do', 'da', 'em', 'um', 'para', 'é', 'com', 'não', 'uma', 'os',  'no', 'se', 
            'na', 'por', 'mais', 'as', 'dos', 'como', 'mas', 'foi', 'ao', 'ele', 'das', 'tem', 'à', 'seu', 'sua', 'ou', 'ser', 'quando', 
            'muito', 'há', 'nos', 'já', 'está', 'eu', 'também', 'só', 'pelo', 'pela', 'até', 'isso', 'ela', 'entre', 'era', 'depois', 
            'sem', 'mesmo', 'aos', 'ter', 'seus', 'quem', 'nas', 'me', 'esse', 'eles', 'estão', 'você', 'tinha', 'foram', 'essa', 'num', 
            'nem', 'suas', 'meu', 'às', 'minha', 'têm', 'numa', 'pelos', 'elas', 'havia', 'seja', 'qual', 'será', 'nós', 'tenho', 'lhe', 
            'deles', 'essas', 'esses', 'pelas', 'este', 'fosse', 'dele', 'tu', 'te', 'vocês', 'vos', 'lhes', 'meus', 'minhas', 'teu', 
            'tua', 'teus', 'tuas', 'nosso', 'nossa', 'nossos', 'nossas', 'dela', 'delas', 'esta', 'estes', 'estas', 'aquele', 'aquela', 
            'aqueles', 'aquelas', 'isto', 'aquilo', 'estou', 'está', 'estamos', 'estão', 'estive', 'esteve', 'estivemos', 'estiveram', 
            'estava', 'estávamos', 'estavam', 'estivera', 'estivéramos', 'esteja', 'estejamos', 'estejam', 'estivesse', 'estivéssemos', 
            'estivessem', 'estiver', 'estivermos', 'estiverem', 'hei', 'há', 'havemos', 'hão', 'houve', 'houvemos', 'houveram', 'houvera', 
            'houvéramos', 'haja', 'hajamos', 'hajam', 'houvesse', 'houvéssemos', 'houvessem', 'houver', 'houvermos', 'houverem', 'houverei',
            'houverá', 'houveremos', 'houverão', 'houveria', 'houveríamos', 'houveriam', 'sou', 'somos', 'são', 'era', 'éramos', 'eram', 
            'fui', 'foi', 'fomos', 'foram', 'fora', 'fôramos', 'seja', 'sejamos', 'sejam', 'fosse', 'fôssemos', 'fossem', 'for', 'formos', 
            'forem', 'serei', 'será', 'seremos', 'serão', 'seria', 'seríamos', 'seriam', 'tenho', 'tem', 'temos', 'tém', 'tinha', 'tínhamos', 
            'tinham', 'tive', 'teve', 'tivemos', 'tiveram', 'tivera', 'tivéramos', 'tenha', 'tenhamos', 'tenham', 'tivesse', 'tivéssemos', 
            'tivessem', 'tiver', 'tivermos', 'tiverem', 'terei', 'terá', 'teremos', 'terão', 'teria', 'teríamos', 'teriam');
    
        return preg_replace('/\b('.implode('|', $stop_words).')\b/', '', $text);
    }
}

if(!function_exists('textToHtml'))
{
    function textToHtml(String $text)
    {
        // To avoid break the HTML
        $text = str_replace("<", "< ", $text);

        $text = str_replace("\n", "<br> ", $text);

        return $text;
    }
}

if(!function_exists('highlightWords'))
{
    function highlightWords(String $text, String $words)
    {
        // To avoid break the HTML
        $text = textToHtml($text);

        $words = removeStopWords($words);
        $words = str_replace("(){}[]&|!*^~?:+", '', $words);
        
        foreach (explode(" ", $words) as $word){
            if($word == "" || $word == " " || strlen($word) == 1)
                continue;

            $text = str_ireplace(" ".$word." ", " <mark>".$word."</mark> ", $text);
        }

        return $text;
    }
}

if(!function_exists('mapjudgment'))
{
    function mapJudgment($judgment, $kappa=False)
    {
        switch ($judgment) {
            case 'Very Relevant':
                return ($kappa)?'d':3;

            case 'Relevant':
                return ($kappa)?'c':2;

            case 'Marginally Relevant':
                return ($kappa)?'b':1;
    
            default:
                return ($kappa)?'a':0;
        }
    }
}