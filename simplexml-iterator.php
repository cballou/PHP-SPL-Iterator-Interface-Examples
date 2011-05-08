<?php
/**
 * This example demonstrates recursively iterating over an XML file
 * to any particular path.
 */

$xmlstring = <<<XML
<?xml version = "1.0" encoding="UTF-8" standalone="yes"?>
<document>
    <animal>
        <category id="26">
            <species>Phascolarctidae</species>
            <type>koala</type>
            <name>Bruce</name>
        </category>
    </animal>
    <animal>
        <category id="27">
            <species>macropod</species>
            <type>kangaroo</type>
            <name>Bruce</name>
        </category>
    </animal>
    <animal>
        <category id="28">
            <species>diprotodon</species>
            <type>wombat</type>
            <name>Bruce</name>
        </category>
    </animal>
    <animal>
        <category id="31">
            <species>macropod</species>
            <type>wallaby</type>
            <name>Bruce</name>
        </category>
    </animal>
    <animal>
        <category id="21">
            <species>dromaius</species>
            <type>emu</type>
            <name>Bruce</name>
        </category>
    </animal>
    <animal>
        <category id="22">
            <species>Apteryx</species>
            <type>kiwi</type>
            <name>Troy</name>
        </category>
    </animal>
    <animal>
        <category id="23">
            <species>kingfisher</species>
            <type>kookaburra</type>
            <name>Bruce</name>
        </category>
    </animal>
    <animal>
        <category id="48">
            <species>monotremes</species>
            <type>platypus</type>
            <name>Bruce</name>
        </category>
    </animal>
    <animal>
        <category id="4">
            <species>arachnid</species>
            <type>funnel web</type>
            <name>Bruce</name>
            <legs>8</legs>
        </category>
    </animal>
</document>
XML;

try {
    
    // load the XML Iterator and iterate over it
    $sxi = new SimpleXMLIterator($xmlstring);
    
    // iterate over animals
    foreach ($sxi as $animal) {
        // iterate over category nodes
        foreach ($animal as $key => $category) {
            echo $category->species . PHP_EOL;
        }
    }

} catch(Exception $e) {
    die($e->getMessage());
}

echo '===================================' . PHP_EOL;
echo 'Finding all species with xpath' . PHP_EOL;
echo '===================================' . PHP_EOL;

// which can also be re-written for optimization
try {
    
    // load the XML Iterator and iterate over it
    $sxi = new SimpleXMLIterator($xmlstring);

    // use xpath
    $foo = $sxi->xpath('animal/category/species');
    foreach ($foo as $k => $v) {
        echo $v . PHP_EOL;
    }

} catch(Exception $e) {
    die($e->getMessage());
}