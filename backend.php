<?php

$url = "https://www.theverge.com";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$html = curl_exec($ch);

curl_close($ch);

if ($html) {
    $dom = new DOMDocument();
    libxml_use_internal_errors(true);
    $dom->loadHTML($html);
    libxml_clear_errors();

    $xpath = new DOMXPath($dom);

    $titles = $xpath->query(".//h2");
    $dates = $xpath->query(".//time | //*[@datetime]");

    $articles = [];

    $startDate = strtotime("2022-01-01");

    for ($i = 0; $i < $titles->length; $i++) {
        $title = $titles->item($i)->nodeValue;
        $dateAttribute = $dates->item($i)->attributes->getNamedItem("datetime");
        if ($dateAttribute) {
            $dateValue = $dateAttribute->nodeValue;
            $articleDate = strtotime($dateValue);

            if ($articleDate >= $startDate) {
                $articles[] = [
                    'title' => $title,
                    'date' => $articleDate, 
                    'formattedDate' => date("Y-m-d", $articleDate) 
                ];
            }
        }
    }

    usort($articles, function ($a, $b) {
        return $a['date'] <=> $b['date'];
    });

    echo "<html><head><style>body { display: flex; justify-content: center; align-items: start; height: 100vh; } table { border-collapse: collapse; } th, td { padding: 8px; text-align: left; border-bottom: 1px solid #ddd; }</style></head><body>";
    echo "<table border='1'><thead><tr><th>Title</th><th>Date</th></tr></thead><tbody>";

    foreach ($articles as $article) {
        echo "<tr><td>{$article['title']}</td><td>{$article['formattedDate']}</td></tr>";
    }

    echo "</tbody></table></body></html>";
} else {
    echo "Failed to retrieve content.";
}
?>

