<?php
 
if (!function_exists('getBanks')) {
    function getBanks()
    {
        $banks = [
            [
                "id" => 1,
                "en" => "National Bank of Abu Dhabi", 
                "ar" => "بنك أبوظبي الوطني", 
            ],
            [
                "id" => 2,
                "en" => "MUFG EMEA", 
                "ar" => "MUFG EMEA", 
            ],
            [
                "id" => 3,
                "en" => "Industrial and Commercial Bank of China Ltd", 
                "ar" => "البنك الصناعي والتجاري الصيني المحدود", 
            ],            
            [
                "id" => 4,
                "en" => "Ziraat Bankası", 
                "ar" => "بنك زراعات التركي", 
            ],            
            [
                "id" => 5,
                "en" => "National Bank of Pakistan", 
                "ar" => "البنك الوطني الباكستاني", 
            ],
            [
                "id" => 6,
                "en" => "J.P. Morgan Bank", 
                "ar" => "جي بي مورغان تشيس", 
            ],
            [
                "id" => 7,
                "en" => "BNP Paribas", 
                "ar" => "بي إن بي باريبا", 
            ],            
            [
                "id" => 8,
                "en" => "Deutsche Bank", 
                "ar" => "دويتشه بنك", 
            ],         
            [
                "id" => 9,
                "en" => "Bank Muscat", 
                "ar" => "بنك مسقط", 
            ],
            [
                "id" => 10,
                "en" => "National Bank of Kuwait", 
                "ar" => "بنك الكويت الوطني", 
            ],
            [
                "id" => 11,
                "en" => "National Bank of Bahrain", 
                "ar" => "بنك البحرين الوطني", 
            ],
            [
                "id" => 12,
                "en" => "Emirates NBD", 
                "ar" => "بنك الإمارات دبي الوطني", 
            ],
            [
                "id" => 13,
                "en" => "Gulf International Bank", 
                "ar" => "بنك الخليج الدولي", 
            ],
            [
                "id" => 14,
                "en" => "Alinma Bank", 
                "ar" => "مصرف الإنماء", 
            ],
            [
                "id" => 15,
                "en" => "Al-Rajhi Bank", 
                "ar" => "مصرف الراجحي", 
            ],
            [
                "id" => 16,
                "en" => "Samba Financial Group", 
                "ar" => "مجموعة سامبا المالية", 
            ],
            [
                "id" => 17,
                "en" => "Riyad Bank", 
                "ar" => "بنك الرياض", 
            ],
            [
                "id" => 18,
                "en" => "Bank AlJazira", 
                "ar" => "بنك الجزيرة", 
            ],
            [
                "id" => 19,
                "en" => "Al Bilad Bank", 
                "ar" => "بنك البلاد", 
            ],
            [
                "id" => 20,
                "en" => "Arab National Bank", 
                "ar" => "البنك العربي الوطني", 
            ],
            [
                "id" => 21,
                "en" => "The Saudi Investment Bank", 
                "ar" => "البنك السعودي للاستثمار", 
            ],
            [
                "id" => 22,
                "en" => "Alawwal Bank", 
                "ar" => "البنك الأول", 
            ],
            [
                "id" => 23,
                "en" => "Banque Saudi Fransi", 
                "ar" => "البنك السعودي الفرنسي", 
            ],
            [
                "id" => 24,
                "en" => "British Saudi Bank", 
                "ar" => "بنك ساب", 
            ],
            [
                "id" => 25,
                "en" => "National Commercial Bank", 
                "ar" => "البنك الأهلي التجاري", 
            ],
        ];
        return $banks;
    }
}

if (!function_exists('round2')) {
    function round2($number)
    {
        $resualt = round($number, 2);
        return $resualt > 0 ? $resualt:0;
    }
}