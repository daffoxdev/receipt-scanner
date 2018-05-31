# Receipt-Scanner

### Uzstādīšanas process
Lai veiktu izstrādāto lietojumprogrammas prototipu uzstādīšanu ir nepieciešams, lai uz timekļa servera būtu uzstādīta rakstzīmju atpazīšanas programma “Tesseract” un PHP 7 nodrošinājms. Tiklīdz šie noteikumi ir izpildīti, nepieciešams kopēt projekta failus uz savā izmantojamā tīmekļa servera. Kad faili ir nokopēti, atrodoties projekta direktorijā, nepieciešams izpildīt “Composer” pakešu pārvaldnieka komandu, kura lejupielādēs bibliotēkas, no kurām ir atkarīga mūsu izveidotā lietojumprogramma. Komandu, kuru nepieciešams izpildīt izmantojot komandrindu: 
```
php composer.phar install
```
Pēc komandas izpildīšanas var sākt izmantot projektu.
