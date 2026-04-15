<?php

namespace App\Services;

class InsideDhakaService
{
    private $place = null;

    private $suburb = null;

    private $thana = null;

    private array $thanas = [
        ['name' => 'Adabor', 'slug' => 'adabor'],
        ['name' => 'Badda', 'slug' => 'badda'],
        ['name' => 'Bangshal', 'slug' => 'bangshal'],
        ['name' => 'Biman Bandar', 'slug' => 'biman-bandar'],
        ['name' => 'Cantonment', 'slug' => 'cantonment'],
        ['name' => 'Chawkbazar', 'slug' => 'chawkbazar'],
        ['name' => 'Darus Salam', 'slug' => 'darus-salam'],
        ['name' => 'Demra', 'slug' => 'demra'],
        ['name' => 'Dhanmondi', 'slug' => 'dhanmondi'],
        ['name' => 'Gendaria', 'slug' => 'gendaria'],
        ['name' => 'Gulshan', 'slug' => 'gulshan'],
        ['name' => 'Hazaribagh', 'slug' => 'hazaribagh'],
        ['name' => 'Jatrabari', 'slug' => 'jatrabari'],
        ['name' => 'Kadamtali', 'slug' => 'kadamtali'],
        ['name' => 'Kafrul', 'slug' => 'kafrul'],
        ['name' => 'Kalabagan', 'slug' => 'kalabagan'],
        ['name' => 'Kamrangirchar', 'slug' => 'kamrangirchar'],
        ['name' => 'Khilgaon', 'slug' => 'khilgaon'],
        ['name' => 'Khilkhet', 'slug' => 'khilkhet'],
        ['name' => 'Kotwali', 'slug' => 'kotwali'],
        ['name' => 'Lalbagh', 'slug' => 'lalbagh'],
        ['name' => 'Mirpur', 'slug' => 'mirpur'],
        ['name' => 'Mohammadpur', 'slug' => 'mohammadpur'],
        ['name' => 'Motijheel', 'slug' => 'motijheel'],
        ['name' => 'New Market', 'slug' => 'new-market'],
        ['name' => 'Pallabi', 'slug' => 'pallabi'],
        ['name' => 'Paltan', 'slug' => 'paltan'],
        ['name' => 'Ramna', 'slug' => 'ramna'],
        ['name' => 'Rampura', 'slug' => 'rampura'],
        ['name' => 'Sabujbagh', 'slug' => 'sabujbagh'],
        ['name' => 'Shah Ali', 'slug' => 'shah-ali'],
        ['name' => 'Shahbag', 'slug' => 'shahbag'],
        ['name' => 'Sher-e-Bangla Nagar', 'slug' => 'sher-e-bangla-nagar'],
        ['name' => 'Shyampur', 'slug' => 'shyampur'],
        ['name' => 'Sutrapur', 'slug' => 'sutrapur'],
        ['name' => 'Tejgaon', 'slug' => 'tejgaon'],
        ['name' => 'Tejgaon Industrial Area', 'slug' => 'tejgaon-industrial-area'],
        ['name' => 'Turag', 'slug' => 'turag'],
        ['name' => 'Uttara East', 'slug' => 'uttara-east'],
        ['name' => 'Uttara West', 'slug' => 'uttara-west'],
        ['name' => 'Vatara', 'slug' => 'vatara'],
        ['name' => 'Wari', 'slug' => 'wari'],
        ['name' => 'Airport', 'slug' => 'airport'],
        ['name' => 'Savar', 'slug' => 'savar'],
        ['name' => 'SabujBagh', 'slug' => 'sabujbagh'],
        ['name' => 'Mugda', 'slug' => 'mugda'],
    ];

    private array $suburbs = [
        ['name' => 'Azimpur', 'slug' => 'azimpur', 'thana_slug' => 'lalbagh'],
        ['name' => 'Gulshan 1', 'slug' => 'gulshan-1', 'thana_slug' => 'gulshan'],
        ['name' => 'Gulshan 2', 'slug' => 'gulshan-2', 'thana_slug' => 'gulshan'],
        ['name' => 'Banani', 'slug' => 'banani', 'thana_slug' => 'gulshan'],
        ['name' => 'Niketan', 'slug' => 'niketan', 'thana_slug' => 'gulshan'],
        ['name' => 'Baridhara', 'slug' => 'baridhara', 'thana_slug' => 'vatara'],
        ['name' => 'Bashundhara R/A', 'slug' => 'bashundhara-ra', 'thana_slug' => 'vatara'],
        ['name' => 'Kuril', 'slug' => 'kuril', 'thana_slug' => 'vatara'],
        ['name' => 'Badda', 'slug' => 'badda', 'thana_slug' => 'badda'],
        ['name' => 'Natun Bazar', 'slug' => 'natun-bazar', 'thana_slug' => 'badda'],
        ['name' => 'Banasree', 'slug' => 'banasree', 'thana_slug' => 'rampura'],
        ['name' => 'Aftabnagar', 'slug' => 'aftabnagar', 'thana_slug' => 'rampura'],
        ['name' => 'Dhanmondi', 'slug' => 'dhanmondi', 'thana_slug' => 'dhanmondi'],
        ['name' => 'Kalabagan', 'slug' => 'kalabagan', 'thana_slug' => 'kalabagan'],
        ['name' => 'Lalmatia', 'slug' => 'lalmatia', 'thana_slug' => 'mohammadpur'],
        ['name' => 'Mohammadpur', 'slug' => 'mohammadpur', 'thana_slug' => 'mohammadpur'],
        ['name' => 'Mirpur-1', 'slug' => 'mirpur-1', 'thana_slug' => 'mirpur'],
        ['name' => 'Mirpur-2', 'slug' => 'mirpur-2', 'thana_slug' => 'mirpur'],
        ['name' => 'Mirpur-10', 'slug' => 'mirpur-10', 'thana_slug' => 'mirpur'],
        ['name' => 'Mirpur-11', 'slug' => 'mirpur-11', 'thana_slug' => 'pallabi'],
        ['name' => 'Mirpur-12', 'slug' => 'mirpur-12', 'thana_slug' => 'pallabi'],
        ['name' => 'Mirpur DOHS', 'slug' => 'mirpur-dohs', 'thana_slug' => 'pallabi'],
        ['name' => 'Farmgate', 'slug' => 'farmgate', 'thana_slug' => 'tejgaon'],
        ['name' => 'Mohakhali', 'slug' => 'mohakhali', 'thana_slug' => 'tejgaon'],
        ['name' => 'Panthapath', 'slug' => 'panthapath', 'thana_slug' => 'tejgaon'],
        ['name' => 'Agargaon', 'slug' => 'agargaon', 'thana_slug' => 'sher-e-bangla-nagar'],
        ['name' => 'Moghbazar', 'slug' => 'moghbazar', 'thana_slug' => 'ramna'],
        ['name' => 'Eskaton', 'slug' => 'eskaton', 'thana_slug' => 'ramna'],
        ['name' => 'Shahbag', 'slug' => 'shahbag', 'thana_slug' => 'shahbag'],
        ['name' => 'Motijheel', 'slug' => 'motijheel', 'thana_slug' => 'motijheel'],
        ['name' => 'Dilkusha', 'slug' => 'dilkusha', 'thana_slug' => 'motijheel'],
        ['name' => 'Paltan', 'slug' => 'paltan', 'thana_slug' => 'paltan'],
        ['name' => 'Wari', 'slug' => 'wari', 'thana_slug' => 'wari'],
        ['name' => 'Sadarghat', 'slug' => 'sadarghat', 'thana_slug' => 'kotwali'],
        ['name' => 'Elephant Road', 'slug' => 'elephant-road', 'thana_slug' => 'new-market'],
        ['name' => 'Lalbagh', 'slug' => 'lalbagh', 'thana_slug' => 'lalbagh'],
        ['name' => 'Sutrapur', 'slug' => 'sutrapur', 'thana_slug' => 'sutrapur'],
        ['name' => 'Dhaka Cantonment', 'slug' => 'dhaka-cantonment', 'thana_slug' => 'cantonment'],
        ['name' => 'Banani DOHS', 'slug' => 'banani-dohs', 'thana_slug' => 'cantonment'],
        ['name' => 'Nikunja', 'slug' => 'nikunja', 'thana_slug' => 'khilkhet'],
        ['name' => 'Uttara', 'slug' => 'uttara', 'thana_slug' => 'uttara-east'],
        ['name' => 'Uttara Sector 7', 'slug' => 'uttara-sector-7', 'thana_slug' => 'uttara-west'],
        ['name' => 'Biman Bandar', 'slug' => 'biman-bandar', 'thana_slug' => 'airport'],
        ['name' => 'Gabtoli', 'slug' => 'gabtoli', 'thana_slug' => 'darus-salam'],
        ['name' => 'Sayedabad', 'slug' => 'sayedabad', 'thana_slug' => 'jatrabari'],
        ['name' => 'Shantinagar', 'slug' => 'Shantinagar', 'thana_slug' => 'paltan'],
        ['name' => 'Amin Bazar', 'slug' => 'amin-bazar', 'thana_slug' => null],
        ['name' => 'Hemayetpur', 'slug' => 'hemayetpur', 'thana_slug' => null],
        ['name' => 'malibagh', 'slug' => 'malibagh', 'thana_slug' => null],
        ['name' => 'Old Dhaka', 'slug' => 'old-dhaka', 'thana_slug' => null],
        ['name' => 'Shajahanpur', 'slug' => 'shajahanpur', 'thana_slug' => null],
        ['name' => 'Basabo', 'slug' => 'basabo', 'thana_slug' => 'sabujbagh'],
        ['name' => 'Mugda', 'slug' => 'mugda', 'thana_slug' => 'mugda'],
    ];

    private array $places = [
        ['name' => 'Jamuna Future Park', 'suburb_slug' => 'bashundhara-ra'],
        ['name' => 'Bashundhara City', 'suburb_slug' => 'panthapath'],
        ['name' => 'Shimanto Shambhar', 'suburb_slug' => 'dhanmondi'],
        ['name' => 'New Market', 'suburb_slug' => 'new-market'],
        ['name' => 'Gausia Market', 'suburb_slug' => 'new-market'],
        ['name' => 'Police Plaza Concord', 'suburb_slug' => 'gulshan-1'],
        ['name' => 'Eastern Plaza', 'suburb_slug' => 'elephant-road'],
        ['name' => 'South City Mall', 'suburb_slug' => 'panthapath'],
        ['name' => 'City Centre', 'suburb_slug' => 'motijheel'],
        ['name' => 'Metro Shopping Mall', 'suburb_slug' => 'mirpur-1'],
        ['name' => 'Twin Tower Complex', 'suburb_slug' => 'shahbag'],
        ['name' => 'Sundarban Shopping Complex', 'suburb_slug' => 'motijheel'],
        ['name' => 'Eastern Mollika', 'suburb_slug' => 'elephant-road'],
        ['name' => 'Mimco Shopping Mall', 'suburb_slug' => 'dhanmondi'],
        ['name' => 'Nandan Mega Mall', 'suburb_slug' => 'bashundhara-ra'],
        ['name' => 'Uttara Trade Centre', 'suburb_slug' => 'uttara'],
        ['name' => 'Bangabazar', 'suburb_slug' => 'paltan'],
        ['name' => 'Evercare Hospital Dhaka', 'suburb_slug' => 'bashundhara-ra'],
        ['name' => 'Labaid Specialized Hospital', 'suburb_slug' => 'dhanmondi'],
        ['name' => 'United Hospital', 'suburb_slug' => 'gulshan-2'],
        ['name' => 'icddr,b', 'suburb_slug' => 'mohakhali'],
        ['name' => 'BSMMU Hospital', 'suburb_slug' => 'shahbag'],
        ['name' => 'BIRDEM General Hospital', 'suburb_slug' => 'shahbag'],
        ['name' => 'Square Hospital', 'suburb_slug' => 'panthapath'],
        ['name' => 'Dhaka Medical College Hospital', 'suburb_slug' => 'shahbag'],
        ['name' => 'Kurmitola General Hospital', 'suburb_slug' => 'dhaka-cantonment'],
        ['name' => 'Apollo Hospital', 'suburb_slug' => 'bashundhara-ra'],
        ['name' => 'Popular Diagnostic Centre', 'suburb_slug' => 'dhanmondi'],
        ['name' => 'Ibn Sina Hospital', 'suburb_slug' => 'dhanmondi'],
        ['name' => 'Bangabandhu Sheikh Mujib Medical University Hospital', 'suburb_slug' => 'shahbag'],
        ['name' => 'National Heart Foundation Hospital', 'suburb_slug' => 'mirpur-1'],
        ['name' => 'Dhaka Shishu Hospital', 'suburb_slug' => 'shahbag'],
        ['name' => 'Holy Family Red Crescent Medical College Hospital', 'suburb_slug' => 'moghbazar'],
        ['name' => 'North South University', 'suburb_slug' => 'bashundhara-ra'],
        ['name' => 'Independent University, Bangladesh', 'suburb_slug' => 'bashundhara-ra'],
        ['name' => 'Brac University', 'suburb_slug' => 'mohakhali'],
        ['name' => 'Dhaka University', 'suburb_slug' => 'shahbag'],
        ['name' => 'Dhaka University (TSC)', 'suburb_slug' => 'shahbag'],
        ['name' => 'BUET', 'suburb_slug' => 'shahbag'],
        ['name' => 'East West University', 'suburb_slug' => 'aftabnagar'],
        ['name' => 'American International University-Bangladesh', 'suburb_slug' => 'kuril'],
        ['name' => 'AIUB', 'suburb_slug' => 'kuril'],
        ['name' => 'University of Asia Pacific', 'suburb_slug' => 'dhanmondi'],
        ['name' => 'Green University', 'suburb_slug' => 'mirpur-1'],
        ['name' => 'Daffodil International University', 'suburb_slug' => 'dhanmondi'],
        ['name' => 'Manarat International University', 'suburb_slug' => 'mirpur-1'],
        ['name' => 'Presidency University', 'suburb_slug' => 'mirpur-1'],
        ['name' => 'State University of Bangladesh', 'suburb_slug' => 'dhanmondi'],
        ['name' => 'Primeasia University', 'suburb_slug' => 'banani'],
        ['name' => 'Mohakhali Bus Terminal', 'suburb_slug' => 'mohakhali'],
        ['name' => 'Kamalapur Railway Station', 'suburb_slug' => 'motijheel'],
        ['name' => 'Sadarghat Launch Terminal', 'suburb_slug' => 'sadarghat'],
        ['name' => 'Gabtoli Bus Terminal', 'suburb_slug' => 'gabtoli'],
        ['name' => 'Sayedabad Bus Terminal', 'suburb_slug' => 'sayedabad'],
        ['name' => 'Airport Road', 'suburb_slug' => 'airport'],
        ['name' => 'Abdullahpur Bus Stand', 'suburb_slug' => 'uttara'],
        ['name' => 'Mohammadpur Bus Stand', 'suburb_slug' => 'mohammadpur'],
        ['name' => 'Jatrabari Bus Stand', 'suburb_slug' => 'jatrabari'],
        ['name' => 'Shyamoli Bus Stand', 'suburb_slug' => 'shyamoli'],
        ['name' => 'Kallyanpur Bus Stand', 'suburb_slug' => 'mirpur-1'],
        ['name' => 'Star Kabab & Restaurant', 'suburb_slug' => 'dhanmondi'],
        ['name' => 'Star Kabab', 'suburb_slug' => 'dhanmondi'],
        ['name' => 'Star Kabab Restaurant', 'suburb_slug' => 'dhanmondi'],
        ['name' => 'Star Kabab Banani', 'suburb_slug' => 'banani'],
        ['name' => 'Star Kabab Restaurant Banani', 'suburb_slug' => 'banani'],
        ['name' => 'Mustakims Kebab & Salims Kebab', 'suburb_slug' => 'mohammadpur'],
        ['name' => 'Dhanmondi Lake', 'suburb_slug' => 'dhanmondi'],
        ['name' => 'Rabindra Sarobar', 'suburb_slug' => 'dhanmondi'],
        ['name' => 'Gulshan Circle 1', 'suburb_slug' => 'gulshan-1'],
        ['name' => 'Gulshan Circle 2', 'suburb_slug' => 'gulshan-2'],
        ['name' => 'The Westin Dhaka', 'suburb_slug' => 'gulshan-2'],
        ['name' => 'Banani Road 11', 'suburb_slug' => 'banani'],
        ['name' => 'Ananda Cinema Hall', 'suburb_slug' => 'farmgate'],
        ['name' => 'Farmgate Police Box', 'suburb_slug' => 'farmgate'],
        ['name' => 'Bangladesh National Museum', 'suburb_slug' => 'shahbag'],
        ['name' => 'Sher-e-Bangla National Cricket Stadium', 'suburb_slug' => 'mirpur-10'],
        ['name' => 'Mirpur Indoor Stadium', 'suburb_slug' => 'mirpur-10'],
        ['name' => 'Lalbagh Fort', 'suburb_slug' => 'lalbagh'],
        ['name' => 'Ahsan Manzil', 'suburb_slug' => 'sutrapur'],
        ['name' => 'National Parliament House', 'suburb_slug' => 'sher-e-bangla-nagar'],
        ['name' => 'Jatiya Sangsad Bhaban', 'suburb_slug' => 'sher-e-bangla-nagar'],
        ['name' => 'Hatirjheel', 'suburb_slug' => 'tejgaon'],
        ['name' => 'National Press Club', 'suburb_slug' => 'paltan'],
        ['name' => 'Bangladesh Secretariat', 'suburb_slug' => 'paltan'],
        ['name' => 'Dhaka Zoo', 'suburb_slug' => 'mirpur-1'],
        ['name' => 'National Botanical Garden', 'suburb_slug' => 'mirpur-1'],
        ['name' => 'Mirpur Zoo', 'suburb_slug' => 'mirpur-1'],
        ['name' => 'Katashur', 'suburb_slug' => 'mohammadpur'],
        ['name' => 'ittadir mor', 'suburb_slug' => 'mohammadpur'],
        ['name' => 'Sat Gambuj Mosque (Seven Domed Mosque)', 'suburb_slug' => 'mohammadpur'],
        ['name' => 'Martyred Intellectuals Memorial (Rayer Bazar Boddhobhumi)', 'suburb_slug' => 'mohammadpur'],
        ['name' => 'Geneva Camp (Bihari Camp)', 'suburb_slug' => 'mohammadpur'],
        ['name' => 'Camper bazar', 'suburb_slug' => 'mohammadpur'],
        ['name' => 'Shia Masjid (Shia Mosque)', 'suburb_slug' => 'mohammadpur'],
        ['name' => 'Asad Gate', 'suburb_slug' => 'mohammadpur'],
        ['name' => 'St. Joseph Higher Secondary School', 'suburb_slug' => 'mohammadpur'],
        ['name' => 'Mohammadpur Preparatory School & College', 'suburb_slug' => 'mohammadpur'],
        ['name' => 'Dhaka Residential Model College (DRMC)', 'suburb_slug' => 'mohammadpur'],
        ['name' => 'Mohammadpur Central College', 'suburb_slug' => 'mohammadpur'],
        ['name' => 'Mohammadpur Government College', 'suburb_slug' => 'mohammadpur'],
        ['name' => 'Krishi Market', 'suburb_slug' => 'mohammadpur'],
        ['name' => 'Tokyo Square', 'suburb_slug' => 'mohammadpur'],
        ['name' => 'Town Hall Market', 'suburb_slug' => 'mohammadpur'],
        ['name' => 'Japan Garden City', 'suburb_slug' => 'mohammadpur'],
        ['name' => 'Tajmahal Road', 'suburb_slug' => 'mohammadpur'],
        ['name' => 'Al-Markazul Islami Hospital', 'suburb_slug' => 'mohammadpur'],
        ['name' => 'City Hospital Ltd.', 'suburb_slug' => 'mohammadpur'],
        ['name' => 'Shaheed Park', 'suburb_slug' => 'mohammadpur'],
        ['name' => 'Chandrima Udyan (Crescent Lake)', 'suburb_slug' => 'mohammadpur'],
        ['name' => 'Baitul Mukarram Mosque', 'suburb_slug' => 'paltan'],
        ['name' => 'National Martyrs Memorial', 'suburb_slug' => 'savar'],
        ['name' => 'Baldha Garden', 'suburb_slug' => 'wari'],
        ['name' => 'Ramna Park', 'suburb_slug' => 'ramna'],
        ['name' => 'Suhrawardy Udyan', 'suburb_slug' => 'shahbag'],
        ['name' => 'Bangabandhu National Stadium', 'suburb_slug' => 'paltan'],
        ['name' => 'Bangladesh Air Force Museum', 'suburb_slug' => 'tejgaon'],
        ['name' => 'Bangladesh National Zoo', 'suburb_slug' => 'mirpur-1'],
        ['name' => 'Bangladesh Shilpakala Academy', 'suburb_slug' => 'segun-bagicha'],
        ['name' => 'Liberation War Museum', 'suburb_slug' => 'sher-e-bangla-nagar'],
        ['name' => 'Bangladesh Railway Museum', 'suburb_slug' => 'poribagh'],
        ['name' => 'Dhaka Club', 'suburb_slug' => 'motijheel'],
        ['name' => 'Radisson Blu Water Garden Hotel', 'suburb_slug' => 'airport'],
        ['name' => 'InterContinental Dhaka', 'suburb_slug' => 'gulshan-1'],
        ['name' => 'Pan Pacific Sonargaon', 'suburb_slug' => 'kakrail'],
        ['name' => 'Begum Bazar', 'suburb_slug' => 'lalbagh'],
        ['name' => 'TSC (Teacher-Student Centre)', 'suburb_slug' => 'shahbag'],
        ['name' => 'Bangladesh China Friendship Conference Center', 'suburb_slug' => 'agargaon'],
        ['name' => 'Bangabhaban', 'suburb_slug' => 'dilkusha'],
    ];

    private array $insideDhakaPostCodes = [
        '1203', // Azimpur
        '1204', // Mohammadpur
        '1205', // Dhaka GPO
        '1206', // Shena nibas
        '1207', // Gulshan
        '1208', // Motijheel
        '1209', // Banani
        '1211', // Mirpur
        '1212', // Dhanmondi
        '1213', // Tejgaon
        '1214', // Dohar
        '1215', // Cantonment
        '1216', // Uttara
        '1217', // Khilgaon
        '1219', // Motijheel
        '1222', // Rampura
        '1223', // Badda
        '1225', // Shantinagar
        '1229', // Baridhara
        '1230', // Uttara West
        '1232', // Wari
        '1236', // Bashundhara
        '1240', // Lalbagh
        '1310', // Sutrapur
        '1311', // Kotwali
        '1312', // Chawkbazar
        '1313', // Lalbagh
        '1314', // Kamrangirchar
        '1315', // Hazaribagh
        '1320', // Shahbag
        '1321', // Ramna
        '1322', // Paltan
        '1323', // Sabujbagh
        '1324', // Demra
        '1325', // Jatrabari
        '1330', // Kadamtali
        '1331', // Shyampur
        '1332', // Gendaria
        '1340', // New Market
        '1341', // Kalabagan
        '1342', // Kafrul
        '1343', // Pallabi
        '1344', // Shah Ali
        '1345', // Darus Salam
        '1346', // Biman Bandar
        '1347', // Khilkhet
        '1348', // Vatara
        '1349', // Turag
        '1350', // Sher-e-Bangla Nagar
        '1351', // Tejgaon Industrial Area
        '1360', // Bangshal
        '1361', // Kotwali
        '1362', // Airport
    ];

    private array $outsideTerms = [
        'faridpur',
        'gazipur',
        'gopalganj',
        'kishoreganj',
        'madaripur',
        'manikganj',
        'munshiganj',
        'narayanganj',
        'narsingdi',
        'rajbari',
        'shariatpur',
        'tangail',
        'bandarban',
        'brahmanbaria',
        'chandpur',
        'chittagong',
        'comilla',
        'coxsb',
        'feni',
        'khagrachhari',
        'lakshmipur',
        'noakhali',
        'rangamati',
        'bagerhat',
        'chuadanga',
        'jessore',
        'jhenaidah',
        'khulna',
        'kushtia',
        'magura',
        'meherpur',
        'narail',
        'satkhira',
        'bogra',
        'chapainawabganj',
        'joypurhat',
        'naogaon',
        'natore',
        'pabna',
        'rajshahi',
        'sirajganj',
        'dinajpur',
        'gaibandha',
        'kurigram',
        'lalmonirhat',
        'nilphamari',
        'panchagarh',
        'rangpur',
        'thakurgaon',
        'habiganj',
        'moulvib',
        'sunamganj',
        'sylhet',
        'barguna',
        'barishal',
        'bhola',
        'jhalokati',
        'patuakhali',
        'pirojpur',
        'jamalpur',
        'mymensingh',
        'netrokona',
        'sherpur',
    ];

    private array $skipWords = [
        'star', // Skip matching "star" with "savar"
        'notre', // Skip matching "notre" with "natore"
        'nitor', // Skip matching "nitor" with "natore"
        'board', // Skip matching "board" with "bogra"
        'para', // Skip matching "para" with "pabna"
        'padma',
        'fari',
    ];

    private array $skipOutsideTermPatterns = [
        // Dhaka-Chittagong Highway (N1)
        'dhaka chittagong highway',
        'dhaka ctg highway',
        'dhaka chittagong hwy',
        'dhaka ctg hwy',
        'dhaka-chittagong highway',
        'dhaka-chittagong hwy',
        'dhaka chittagong rd',
        'dhaka chittagong road',
        'daka chittagong highway',
        'dhaka chitagong highway',
        'dhaka chitagong hwy',
        'dhaka chottogram highway',
        'dhaka chottogram hwy',
        'dhaka chotogram hwy',
        'dhaka ctg rd',
        'dacca chittagong highway',
        'dakka chittagong highway',
        'dhaka to ctg highway',

        // Dhaka-Mymensingh Highway (N3)
        'dhaka mymensingh highway',
        'dhaka mymensingh hwy',
        'dhaka-mymensingh highway',
        'dhaka momensingh highway',
        'dhaka momenshing hwy',
        'dhaka mimensingh highway',
        'daka mymensingh hwy',

        // Dhaka-Tangail-Jamalpur Highway (N4)
        'dhaka tangail highway',
        'dhaka tangail hwy',
        'dhaka-tangail highway',
        'dhaka tangail jamalpur highway',
        'dhaka tangail jamalpur hwy',
        'dhaka to tangail highway',
        'daka tangail hwy',
        'dhaka tangil highway',

        // Dhaka-Sylhet Highway (N2)
        'dhaka sylhet highway',
        'dhaka sylhet hwy',
        'dhaka-sylhet highway',
        'dhaka shilhet highway',
        'dhaka shilet hwy',
        'dhaka to sylhet highway',
        'daka sylhet hwy',
        'dhaka silet highway',

        // Dhaka-Aricha Highway (N5)
        'dhaka aricha highway',
        'dhaka aricha hwy',
        'dhaka-aricha highway',
        'dhaka to aricha highway',
        'dhaka aricha road',
        'daka aricha hwy',
        'dhaka aricha rd',
        'dhaka aricha bypass',

        // Dhaka-Khulna Highway (N8)
        'dhaka khulna highway',
        'dhaka khulna hwy',
        'dhaka-khulna highway',
        'dhaka to khulna highway',
        'dhaka khulna road',
        'daka khulna hwy',
        'dhaka kulna highway',
        'dhaka khulna rd',

        // Common typos and abbreviations
        'dhaka highway',
        'dhaka hwy',
        'dhaka rd',
        'daka rd',
        'daka hwy',
        'dhaka road',
        'dhaka bypass',
        'dhaka bypass road',
        'ctg highway',
        'ctg hwy',
        'dacca road',
        'dacca hwy',
        'dakka road',
        'dakka hwy',
        'dhaka by pass',
        'dhaka outer ring road',
        'narayanganj bahadurabad line',
        'narayanganj bahadurabad ghat line',

        // Regional highways
        'chittagong cox bazar highway',
        'ctg cox bazar hwy',
        'chittagong-coxs bazar highway',
        'chittagong coxs bazar hwy',
        'ctg cox s bazar highway',
        'chittagong cox s bazar road',
        'barisal patuakhali highway',
        'barisal patuakhali hwy',
        'barisal-patuakhali road',
        'rangpur dinajpur highway',
        'rangpur dinajpur hwy',
        'rangpur-dinajpur road',
        'bogura sirajganj highway',
        'bogura sirajganj hwy',
        'bogra sirajganj highway',

        // Numbered highways (N1-N8)
        'n1 highway',
        'n 1 highway',
        'n-1 highway',
        'n1 hwy',
        'national highway n1',
        'n2 highway',
        'n3 highway',
        'n4 highway',
        'n5 highway',
        'n6 highway',
        'n7 highway',
        'n8 highway',
        'n 2 hwy',
        'n-3 road',
        'national highway n4',
        'n5 hwy',
        'n 6 highway',
        'n-7 hwy',
        'n8 rd',
    ];

    private function normalizeString($str): string
    {
        $normalized = strtolower(trim($str));
        $normalized = preg_replace('/[^a-z0-9]/', ' ', $normalized);
        $normalized = preg_replace('/\s+/', ' ', $normalized);

        return trim($normalized);
    }

    private function checkIfExists($string, $terms, $isStrict = false)
    {
        $normalizedString = $this->normalizeString($string);
        $stringLength     = strlen($normalizedString);

        foreach ($terms as $term) {
            $normalizedTerm = is_array($term) ? $this->normalizeString($term['name']) : $this->normalizeString($term);
            $termLength     = strlen($normalizedTerm);

            // Skip if first character doesn't match
            if (substr($normalizedString, 0, 1) !== substr($normalizedTerm, 0, 1)) {
                continue;
            }

            // Length-based check: terms must be within 2 characters of each other
            if (abs($stringLength - $termLength) > 2) {
                continue;
            }

            // Stricter matching for skipWords and outsideTerms
            $levenshteinThreshold = $isStrict ? 1 : 2; // Stricter for skipWords/outsideTerms (<=1), looser for others (<3)
            if (levenshtein($normalizedString, $normalizedTerm) <= $levenshteinThreshold) {
                return is_array($term) ? $term : $term;
            }
        }

        return false;
    }

    private function checkPlace($input): bool
    {
        $place = $this->checkIfExists($input, $this->places);
        if ($place) {
            $this->place = $place;

            return true;
        }

        return false;
    }

    private function checkSuburb($input): bool
    {
        $suburb = $this->checkIfExists($input, $this->suburbs);
        if ($suburb) {
            $this->suburb = $suburb;

            return true;
        }

        return false;
    }

    private function checkThana($input): bool
    {
        $thana = $this->checkIfExists($input, $this->thanas);
        if ($thana) {
            $this->thana = $thana;

            return true;
        }

        return false;
    }

    private function getSubstrings($address): array
    {
        $words      = explode(' ', $address);
        $substrings = [];
        for ($i = 0; $i < count($words); $i++) {
            for ($j = 1; $j <= count($words) - $i; $j++) {
                $substring    = implode(' ', array_slice($words, $i, $j));
                $substrings[] = $substring;
                // Also add hyphenated version
                $hyphenated = str_replace(' ', '-', $substring);
                if ($hyphenated !== $substring) {
                    $substrings[] = $hyphenated;
                }
            }
        }

        return array_filter($substrings);
    }

    public function isInsideDhaka($address): bool
    {
        $inside_dhaka = false;

        $address = $this->normalizeString($address);

        // Remove skipOutsideTermPatterns
        foreach ($this->skipOutsideTermPatterns as $pattern) {
            $address = str_replace($this->normalizeString($pattern), '', $address);
            $address = preg_replace('/\s+/', ' ', $address);
        }

        // Remove skipWords
        foreach ($this->skipWords as $word) {
            $address = str_replace($this->normalizeString($word), '', $address);
            $address = preg_replace('/\s+/', ' ', $address);
        }

        $wordsArray = explode(' ', $address);
        $substrings = $this->getSubstrings($address);

        // Check for outside terms with strict matching
        foreach ($wordsArray as $word) {
            $matchedOutsideTerm = $this->checkIfExists($word, $this->outsideTerms, true);
            if ($matchedOutsideTerm) {
                return false;
            }
        }

        // Check substrings for places, suburbs, and thanas
        foreach ($substrings as $substring) {
            if ($this->checkPlace($substring)) {
                $inside_dhaka = true;
            }
            if ($this->checkSuburb($substring)) {
                $inside_dhaka = true;
            }
            if ($this->checkThana($substring)) {
                $inside_dhaka = true;
            }
        }

        // Second check for outside terms to ensure no false positives
        foreach ($wordsArray as $word) {
            $matchedOutsideTerm = $this->checkIfExists($word, $this->outsideTerms, true);
            if ($matchedOutsideTerm) {
                return false;
            }
        }

        // Infer thana from suburb if available
        if ($this->suburb && ! $this->thana && ! empty($this->suburb['thana_slug'])) {
            foreach ($this->thanas as $thana) {
                if ($thana['slug'] === $this->suburb['thana_slug']) {
                    $this->thana = $thana;

                    break;
                }
            }
        }

        // Check for postal codes
        foreach ($wordsArray as $word) {
            if (in_array($word, $this->insideDhakaPostCodes)) {
                $inside_dhaka = true;
            }
        }

        return $inside_dhaka;
    }
}
