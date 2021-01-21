<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;

class cil_import_editions extends Command
{
    protected $signature    = 'cil:import_editions';
    protected $description  = 'Import Editions to MySQL-DB';
    public function __construct() { parent::__construct(); }

    // -----------------------------------------------------------------------
    static $editions = [
        // CIL
        ['WE0010', 'I',             'CIL I: Inscriptiones antiquissimae ed. alt.'],
        ['WE0010', 'I2',            'CIL I²: Inscriptiones antiquissimae ed. alt.'],
        ['WE0010', 'II',            'CIL II: Inscriptiones Hispaniae'],
        ['WE0010', 'II2/5',         'CIL II²/5: Inscriptiones Hispaniae ed. alt. Conventus Astigitanus'],
        ['WE0010', 'II2/7',         'CIL II²/7: Inscriptiones Hispaniae ed. alt. Conventus Cordubensis'],
        ['WE0010', 'II2/13',        'CIL II²/13: Inscriptiones Hispaniae ed. alt. Conventus Carthaginiensis'],
        ['WE0010', 'II2/14',        'CIL II²/14: Inscriptiones Hispaniae ed. alt. Conventus Tarraconensis'],
        ['WE0010', 'III',           'CIL III: Inscriptiones Asiae, provinciarum Europae Graecarum, Illyrici'],
        ['WE0010', 'IV',            'CIL IV: Inscriptiones parietariae Pompeianae Herculanenses Stabianae'],
        ['WE0010', 'V',             'CIL V: Inscriptiones Galliae Cisalpinae'],
        ['WE0010', 'VI',            'CIL VI: Inscriptiones urbis Romae'],
        ['WE0010', 'VII',           'CIL VII: Inscriptiones Britanniae'],
        ['WE0010', 'VIII',          'CIL VIII: Inscriptiones Africae'],
        ['WE0010', 'IX',            'CIL IX: Inscriptiones Calabriae, Apuliae, Samnii, Sabinorum, Piceni'],
        ['WE0010', 'X',             'CIL X: Inscriptiones Bruttiorum, Lucaniae, Campaniae, Siciliae, Sardiniae'],
        ['WE0010', 'XI',            'CIL XI: Inscriptiones Aemiliae, Etruriae, Umbriae'],
        ['WE0010', 'XII',           'CIL XII: Inscriptiones Galliae Narbonensis'],
        ['WE0010', 'XIII',          'CIL XIII: Inscriptiones trium Galliarum et Germaniarum'],

        // CIL Additions
        ['WE0011', 'XIII N. I',     'Finke, Neue Inschriften. Nachträge zu den neuen Inschriften. Register zu den neuen Inschriften nebst Nachträgen. Bericht der Römisch-Germanischen Kommission 17, 1927, 1*–2*. 1–107. 198–231'],
        ['WE0012', 'XIII N. II',    'Nesselhauf, Neue Inschriften aus dem römischen Germanien und den angrenzenden Gebieten. Bericht der Römisch-Germanischen Kommission 27, 1937, 51–134'],
        ['WE0013', 'XIII N. III',   'Herbert Nesselhauf; Lieb, Dritter Nachtrag zu CIL. XIII. Inschriften aus den germanischen Provinzen und dem Treverergebiet. Bericht der Römisch-Germanischen Kommission 40, 1959, 120–229'],
        ['WE0014', 'XIII N. IV',    'Schillinger-Häfele, Vierter Nachtrag zu CIL XIII und zweiter Nachtrag zu Fr. Vollmer, Inscriptiones Baivariae Romanae. Inschriften aus dem deutschen Anteil der germanischen Provinzen und des Treverergebietes sowie Rätiens und Noricums. Bericht der Römisch-Germanischen Kommission 58, 1977, 447–603'],

        // CIL
        ['WE0010', 'XIV',           'CIL XIV: Inscriptiones Latii veteris'],
        ['WE0010', 'XV',            'CIL XV: Inscriptiones urbis Romae. Instrumentum domesticum'],
        ['WE0010', 'XVI',           'CIL XVI: Diplomata militaria'],
        ['WE0010', 'XVII/1',        'CIL XVII/1: Miliaria imperii Romani. Provinciae Hispaniae et Britannia'],
        ['WE0010', 'XVII/2',        'CIL XVII/2: Miliaria provinciarum Narbonensis Galliarum Germaniarum'],
        ['WE0010', 'XVII/4',        'CIL XVII/4: Miliaria imperii Romani. Illyricum et provinciae Europae Graecae'],

        // Other Editions
        ["WE0001", "AE",            "L'Année épigraphique"],
        ["WE0002", "AIJ",           "Antike Inschriften aus Jugoslavien. Heft 1. Noricum und Pannonia superior.  Bearbeitet von Viktor Hoffiller und Balduin Saria"],
        //+
        ["WE0000", "BAC",           "BAC"],

        ["WE0114", "BCAR",          "Bullettino della Commissione Archeologica Comunale di Roma"],
        ["WE0003", "Besevliev",     "Beševliev, Spätgriechische und spätlateinische Inschriften aus Bulgarien"],
        ["WE0004", "Bloch",         "Bloch, Supplement to volume XV, 1 of the Corpus Inscriptionum Latinarum including complete indices to the Roman brick-stamps (1948) [= The Roman brick-stamps not published in volume XV 1 of the „Corpus Inscriptionum Latinarum“, HSCP 56–57, 1947, 1–128 + Indices to the Roman brick-stamps published in volume XV 1 of the Corpus Inscriptionum Latinarum and LVI–LVII of the Harvard Studies in Classical Philology, HSCP 58–59, 1948, 1–104]"],
        ["WE0005", "Bruns",         "Fontes iuris Romani antiqui. Edidit Carolus Georgius Bruns. Post curas Theodori Mommseni editionibus quintae et sextae adhibitas septimum edidit Otto Gradenwitz. Pars prior. Leges et negotia (1909)"],
        ["WE0102", "CBFIR",         "Der römische Weihebezirk von Osterburken I. Schallmayer; Eibl; Ott; Preuss; Wittkopf, Corpus der griechischen und lateinischen Beneficiarier-Inschriften des Römischen Reiches"],
        ["WE0006", "Ceska/Hosek",   "Češka; Hošek, Inscriptiones Pannoniae superioris in Slovacia Transdanubiana asservatae"],
        ["WE0123", "Cholodniak",    "Cholodniak, Carmina sepulcralia Latina epigraphica"],
        ["WE0103", "CIB",           "Veny, Corpus de las inscripciones balearicas hasta la domincion arabe"],
        ["WE0007", "CIE",           "Corpus Inscriptionum Etruscarum"],
        ["WE0008", "CIG",           "Corpus Inscriptionum Graecarum"],
        ["WE0098", "CIGP",          "Kovács, Corpus Inscriptionum Graecarum Pannonicarum"],
        ["WE0009", "CII",           "Corpus Inscriptionum Iudaicarum. Recueil des inscriptions juives qui vont du IIIe siècle avant Jésus-Christ au VIIe siècle de notre ère. Vol. I. Europe. Par Jean-Baptiste Frey (1936) [= Corpus of Jewish inscriptions. Jewish inscriptions from the third century B.C. to the seventh century A.D. Volume I. Europe (1975)]"],
        ["WE0149", "CIIP",          "Corpus inscriptionum Iudaeae/Palestinae. A multi-lingual corpus of the inscriptions from Alexander to Muhammad"],
        ["WE0015", "CILA",          "CILA 1 = Corpus de Inscripciones latinas de Andalucía 1. Huelva (1989). CILA 2 = Corpus de Inscripciones latinas de Andalucía 2. Sevilla [1 (1991): n. 1–338. 2 (1991): n. 339–610; 1*–26*. 3 (1996): n. 611–1011. 4 (1996): n. 1012–1255; 27*–52*]. CILA 3 = Corpus de Inscripciones latinas de Andalucía 3. Jaén [1 (1991): n. 1–355,. 2 (1991): n. 356–645]. CILA 4 = Corpus de Inscripciones latinas de Andalucía 4. Granada (2002)"],
        ["WE0016", "CIMRM",         "Vermaseren, Corpus inscriptionum et monumentorum religionis Mithriacae"],
        ["WE0017", "CLE",           "Buecheler, Carmina Latina epigraphica"],
        ["WE0018", "Corinth VIII 2", "Corinth. Results of Excavations conducted by the American School of Classical Studies at Athens. Volume VIII, Part II. Latin inscriptions 1896–1926 edited by Allen Brown West"],
        ["WE0110", "Corinth VIII 3", "Corinth. Results of Excavations conducted by the American School of Classical Studies at Athens. Volume VIII, Part III. The inscriptions 1926–1960 edited by John Harvey Kent"],
        ["WE0097", "Dobo",          "Inscriptiones extra fines Pannoniae Daciaeque repertae ad res earundem provinciarum pertinentes quas collegit adnotationibusque instruxit Arpadus Dobó. Editio quarta aucta et emendata"],
        ["WE0119", "Dougga",        "Dougga, fragments d’histoire. Choix d’inscriptions latines éditées, traduites et commentées (Ier – IVe siècles). Sous la direction de Mustapha Khanoussi et Louis Maurin"],
        ["WE0019", "EE",            "Ephemeris epigraphica"],
        ["WE0121", "Engström",      "Carmina Latina Epigraphica post editam collectionem Buechelerianam in lucem prolata conlegit Einar Engström"],
        ["WE0117", "ERCanosa",      "Le epigrafi romane di Canosa. A cura di Marcella Chelotti, Rosanna Gaeta, Vincenza Morizio, Marina Silvestrini. Coordinatori Francesco Grelle, Mario Pani"],
        ["WE0020", "Fiebiger - Schmidt", "Fiebiger; Schmidt, Inschriftensammlung zur Geschichte der Ostgermanen"],
        ["WE0021", "Fiebiger - Schmidt II", "Fiebiger; Schmidt, Inschriftensammlung zur Geschichte der Ostgermanen. Neue Folge"],
        ["WE0093", "Fiebiger - Schmidt III", "Fiebiger; Schmidt, Inschriftensammlung zur Geschichte der Ostgermanen. Zweite Folge"],
        ["WE0022", "FIRA",          "Fontes iuris Romani Anteiustininiani"],
        ["WE0023", "French, Rom. Roads and Milestones of Asia Minor", "French, Roman Roads and Milestones of Asia Minor. Fasc. 2. An interim catalogue of Milestones"],
        ["WE0024", "Gerstl, Suppl. CIL III", "Gerstl, Supplementum epigraphicum zu CIL III für Kärnten und Osttirol 1902–1961"],
        ["WE0147", "GLISwedish",    "Thomasson, A Survey of Greek and Latin Inscriptions on Stone in Swedish Collections"],
        ["WE0113", "Graffiti del Palatino", "Graffiti del Palatino. I. Paedagogium a cura di Heikki Solin e Marja Itkonen-Kaila (1966). II. Domus Tiberiana a cura di Paavo Castrén e Henrik Lilius (1970)"],
        ["WE0101", "HAE",           "Hispania Antiqua Epigraphica"],
        ["WE0100", "HEp.",          "Hispania Epigraphica"],
        ["WE0025", "Hild, Suppl. CIL III", "Hild, Supplementum epigraphicum zu CIL III. Das pannonische Niederösterreich, Burgenland und Wien 1902–1968"],
        ["WE0026", "Howald/Meyer",  "Howald; Meyer, Die römische Schweiz"],
        ["WE0092", "I. v. Perge",   "Inschriften griechischer Städte aus Kleinasien. Bd. 54. Die Inschriften von Perge. T. I. (Vorrömische Zeit, frühe und hohe Kaiserzeit). Herausgegeben von Sencer Şahin"],

        // These two were originally not divided
        ["WE0027", "IAM II",        "Inscriptions antiques du Maroc. II. Inscriptions latines (1982)"],
        ["WE0027", "IAM II Suppl.", "Inscriptions antiques du Maroc. II. Inscriptions latines (1982). Supplément (2003)"],

        ["WE0104", "IAmpur.",       "Almagro, Las inscripciones ampuritanas griegas, ibéricas y latinas"],
        ["WE0028", "IBC",           "Hübner, Inscriptiones Britanniae Christianae"],
        ["WE0029", "ICERV",         "Vives, Inscripciones cristianas de la España romana y visigoda"],
        ["WE0094", "ICG",           "Le Blant, Inscriptions chrétiennes de la Gaule antérieures au VIIIe siècle. Tome I. Provinces Gallicanes (1856). Tome II. Les Sept provinces (1865)"],
        ["WE0030", "ICI",           "Inscriptiones Christianae Italiae septimo saeculo antiquiores"],
        ["WE0108", "ICret",         "Guarducci, Inscriptiones Creticae: I. Tituli Cretae mediae praeter Gortynios (1935). II. Tituli Cretae occidentalis (1939). III. Tituli Cretae orientalis (1942). Tituli Gortynii (1950)"],
        ["WE0031", "ICVR",          "de Rossi, Inscriptiones christianae urbis Romae septimo saeculo antiquiores"],
        ["WE0032", "ICVR n. s.",    "Silvagni; Ferrua, Inscriptiones christianae urbis Romae septimo saeculo antiquiores"],
        ["WE0033", "IDR",           "Inscriptiile Daciei Romane. I. Prolegomena historica et epigraphica diplomata militaria, tabulae ceratae. II. Pars meridionalis, inter Danuvium et Carpatos montes. III. Dacia Superior. 1. Pars occidentalis (ager inter Danuvium, Pathisium Marisiamquae). 2. Ulpia Traiana Dacica (Sarmizegetusa). 3. Pars media (ager inter Ulpiam Traianam, Miciam, Apulum, Alburnum Maiorem et flumen Crisium). 4. Pars orientalis. 5. Apulum. 6. Apulum – Instrumentum domesticum"],
        ["WE0034", "IG",            "Inscriptiones Graecae"],
        ["WE0035", "IGB",           "Michajlov, Inscriptiones Graecae in Bulgaria repertae. Volumen I. Inscriptiones orae Ponti Euxini. Editio altera emendata"],
        ["WE0036", "IGLS",          "Inscriptions grecques et latines de la Syrie. I. Commagène et Cyrrhestique (1929) [n. 1–256]. II. Chalcidique et Antiochène (1939) [n. 257–698]. III, 1. Région de l’Amanus. Antioche (1950) [. 699–988]. III, 2. Antioche (Suite). Antiochene (1953) [n. 989–1242]. IV. Laodicée. Apamène (1955) [n. 1243–1997]. V. Émésène (1959) [n. 1998–2710]. VI. Baalbek et Beqa‘ (1967) [n. 2711–3017]. VII. Arados et régions voisines (1970) [n. 4001–4061]. VIII, 3. Les inscriptions forestières d’Hadrien dans le Mont Liban (1980). XIII, 1. Bostra (1982) [n. 9001–9472]. XXI. Inscriptions de la Jordanie II. Région centrale (1986). XXI. Inscriptions de la Jordanie IV. Pétra et la Nabatène méridionale (1993). XXI. Inscriptions de la Jordanie V. La Jordanie du Nord-Est 1 (2009)"],
        ["WE0037", "IGRR",          "Cagnat, Inscriptiones Graecae ad res Romanas pertinentes"],
        ["WE0038", "IGUR",          "Moretti, Inscriptiones Graecae urbis Romae"],
        ["WE0039", "IHC",           "Hübner, Inscriptiones Hispaniae Christianae"],
        ["WE0111", "IJO",           "Inscriptiones Judaicae Orientis. Volume I. Eastern Europe. Band II. Kleinasien. Volume III. Syria and Cyprus"],
        ["WE0040", "IK",            "Inschriften griechischer Städte aus Kleinasien"],
        ["WE0152", "IKöln2",        "Galsterer, B.; Galsterer, H., Die römischen Steininschriften aus Köln. IKöln2"],
        ["WE0138", "ILA Arvernes",  "Inscriptions Latines d’Aquitaine (I.L.A.). Rémy, Arvernes"],
        ["WE0145", "ILA Bordeaux",  "Inscriptions Latines d’Aquitaine (I.L.A.). Maurin; Navarro Caballero, Bordeaux"],
        ["WE0139", "ILA Lectoure",  "Inscriptions Latines d’Aquitaine (I.L.A.). Fabre; Sillières, Lectoure"],
        ["WE0135", "ILA Nitiobroges", "Inscriptions Latines d’Aquitaine (I.L.A.). Fages; Maurin, Nitiobroges"],
        ["WE0140", "ILA Pétrucores", "Inscriptions Latines d’Aquitaine (I.L.A.). Bost; Fabre, Pétrucores"],
        ["WE0136", "ILA Santons",   "Inscriptions Latines d’Aquitaine (I.L.A.). Thauré; Tassaux, Santons"],
        ["WE0137", "ILA Vellaves",  "Inscriptions Latines d’Aquitaine (I.L.A.). Rémy, Vellaves"],
        ["WE0041", "ILAfr.",        "Cagnat; Merlin; Chatleain, Inscriptions latines d’Afrique (Tripolitaine, Tunisie, Maroc)"],
        ["WE0042", "ILAlg.",        "Gsell, Inscriptions latines de l’Algérie"],
        ["WE0148", "ILAlpes",       "Inscriptions Latines des Alpes  (I.L.Alpes). I. Rémy, Alpes Graies"],
        ["WE0106", "ILB",           "Gerov, Inscriptiones Latinae in Bulgaria repertae (Inscriptiones inter Oescum et Iatrum repertae) curante  Mihailov"],
        ["WE0043", "ILCV",          "Diehl, Inscriptiones Latinae Christianae veteres"],
        ["WE0044", "ILER",          "Vives, Inscripciones latinas de la España romana"],
        ["WE0045", "ILGN",          "Espérandieu, Inscriptions latines de Gaule (Narbonnaise)"],
        ["WE0046", "ILGR",          "Šašel Kos, Inscriptiones Latinae in Graecia repertae. Additamenta ad CIL III"],
        ["WE0047", "ILJug.",        "ILJug. I = Inscriptiones Latinae quae in Iugoslavia inter annos MCMXL et MCMLX repertae et editae sunt  (1963) [n. 1–451]. ILJug. II = Inscriptiones Latinae quae in Iugoslavia inter annos MCMLX et MCMLXX repertae et editae sunt  (1978) [n. 452–1222]. ILJug. III = Inscriptiones Latinae quae in Iugoslavia inter annos MCMII et MCMXL repertae et editae sunt  (1986) [n. 1223–3128]"],
        ["WE0048", "ILLPRON",       "Inscriptionum lapidariarum latinarum provinciae Norici usque ad annum MCMLXXXIV repertarum indices. Fasciculus primus. Catalogus"],
        ["WE0049", "ILLRP",         "Degrassi, Inscriptiones Latinae liberae rei publicae"],
        ["WE0050", "ILM",           "Chatelain, Inscriptions latines du Maroc"],
        ["WE0128", "ILN I Frèjus",  "Inscriptions latines de Narbonnaise (I.L.N.). [I.] Gascou; Janon, Fréjus"],
        ["WE0129", "ILN II Antibes", "Inscriptions latines de Narbonnaise (I.L.N.). II. Chastagnol, Antibes, Riez, Digne"],
        ["WE0131", "ILN II Digne",  "Inscriptions latines de Narbonnaise (I.L.N.). II. Chastagnol, Antibes, Riez, Digne"],
        ["WE0130", "ILN II Riez",   "Inscriptions latines de Narbonnaise (I.L.N.). II. Chastagnol, Antibes, Riez, Digne"],
        ["WE0132", "ILN III Aix-en-Provence", "Inscriptions latines de Narbonnaise (I.L.N.). III. Gascou, Aix-en-Provence"],
        ["WE0133", "ILN IV Apt",    "Inscriptions latines de Narbonnaise (I.L.N.). IV. Gascou; Leveau; Rimbert, Apt."],
        //+
        ["WE0133", "ILN VI Alba",    "Inscriptions latines de Narbonnaise (I.L.N.). VI."],
        // Splitted
        //["WE0134", "ILN V Vienne",  "Inscriptions latines de Narbonnaise (I.L.N.). V. Vienne. Sous la direction de Bernard Rémy. [V. 1 2004; V. 2 2004; V. 3 2005]"],
        ["WE0134", "ILN V. 1 Vienne",  "Inscriptions latines de Narbonnaise (I.L.N.). V. Vienne. Sous la direction de Bernard Rémy. 1 2003"],
        ["WE0134", "ILN V. 2 Vienne",  "Inscriptions latines de Narbonnaise (I.L.N.). V. Vienne. Sous la direction de Bernard Rémy. 2 2004"],
        ["WE0134", "ILN V. 3 Vienne",  "Inscriptions latines de Narbonnaise (I.L.N.). V. Vienne. Sous la direction de Bernard Rémy. 3 2005"],
        //["WE0146", "ILN VII Les Vocones", "Inscriptions latines de Narbonnaise (I.L.N.). VII. Rémy; Desaye, Les Voconces. [VII 1. Die 2012]"],
        ["WE0146", "ILN VII. 1 Die", "Inscriptions latines de Narbonnaise (I.L.N.). VII. Rémy; Desaye, Les Voconces. 1. Die 2012"],

        ["WE0151", "ILP",           "Mello; Voza, Le iscrizioni latine di Paestum"],
        ["WE0099", "ILPG",          "Pastor Muñoz; Mendoza Eguaras, Inscripciones latinas de la provincia de Granada"],
        ["WE0051", "ILS",           "Dessau, Inscriptiones Latinae selectae"],
        ["WE0083", "ILSard.",       "Sotgiu, Iscrizioni latine della Sardegna (Supplemento al Corpus Inscriptionum Latinarum, X e all’Ephemeris Epigraphica, VIII.)"],
        ["WE0052", "ILSl",          "Inscriptiones Latinae Sloveniae"],
        ["WE0053", "ILT",           "Merlin, Inscriptions latines de la Tunisie"],
        ["WE0054", "ILTG",          "Wuilleumier, Inscriptions latines des trois Gaules (France)"],
        ["WE0055", "IMS",           "Inscriptions de la Mésie Supérieure. I. Singidunum et le Nord-Ouest de la province. II. Viminacium et Margum. III. 2. Timacum Minus et la vallée du Timok. IV. Naissus – Remesiana – Horreum Margi. VI. Scupi et la Région de Kumanovo"],
        ["WE0109", "Inscr. Aq.",    "Brusin, Inscriptiones Aquileiae"],
        ["WE0056", "Inscr. It.",    "Inscriptiones Italiae"],
        ["WE0120", "Inscr. Saloni.", "Bulić, Inscriptiones quae in C. R. Museo archaeologico Salonitano Spalati asservantur"],
        ["WE0057", "IOSPE",         "Latysev, Inscriptiones antiquae orae septentrionalis Ponti Euxini Graecae et Latinae"],
        ["WE0107", "IRAlm.",        "Lázaro Pérez, Inscripciones romanas de Almeria"],
        ["WE0105", "IRC",           "Inscriptions romaines de Catalogne. I. Barcelone (sauf Barcino). II. Lérida. III. Gérone. IV. Barcino"],
        ["WE0058", "IRCP",          "d'Encarnaçao, Inscriçoes romanas do conventus Pacensis"],
        ["WE0059", "IRN",           "Mommsen, Inscriptiones regni Neapolitani latinae"],
        ["WE0060", "IRT",           "Reynolds; Ward Perkins, The Inscriptions of Roman Tripolitania"],
        ["WE0061", "ISard.",        "Sardis. Publications of the American Society for the excavation of Sardis. Volume VII. Greek and Latin inscriptions"],
        ["WE0062", "ISM",           "Inscriptiile din Scythia Minor grecesti si latine"],
        ["WE0063", "IvE",           "Die Inschriften von Ephesos"],
        ["WE0112", "IvM",           "Inschriften von Milet"],
        ["WE0064", "IvPergamon",    "Fränkel, Die Inschriften von Pergamon"],
        ["WE0065", "JIWE",          "Noy, Jewish inscriptions of Western Europe. Volume I. Italy (excluding the City of Rome), Spain and Gaul. Volume II. The City of Rome"],
        ["WE0066", "Kränzel - Weber (1997)", "Kränzel; Weber, Die römerzeitlichen Inschriften aus Rom und Italien in Österreich"],
        ["WE0067", "Lehner",        "Lehner, Die antiken Steindenkmäler des Provinzialmuseums in Bonn"],
        //+
        ["WE0000", "LIA",           "Corpus des inscriptions latines d’Albanie"],

        ["WE0068", "MAMA",          "Monumenta Asiae Minoris antiqua"],
        ["WE0143", "Miliarios Tarraconense", "Lostal Pros, Los miliarios de la provincia tarraconense (Conventos tarraconense, cesaraugustano, cluniense y cartaginense)"],
        ["WE0118", "Mourir à Dougga", "Mourir à Dougga. Recueil des inscriptions funéraires. Sous la direction de Mustapha Khanoussi et Louis Maurin"],
        ["WE0150", "Novae",         "Inscriptions latines de Novae. Par Violeta Božilova, Jerzy Kolendo et Leszek Mrozewicz. Sous la rédaction de Jerzy Kolendo"],
        ["WE0095", "NRICG",         "Le Blant, Nouveau recueil des inscriptions chrétiennes de la Gaule antérieures au VIIIe siècle"],
        ["WE0070", "NS",            "Atti della Accademia Nazionale dei Lincei. Notizie degli scavi di antichità"],
        ["WE0071", "OGIS",          "Dittenberger, Orientis Graeci inscriptiones selectae"],
        ["WE0072", "Oxé/Comfort",   "Oxé; Comfort, Corpus vasorum Arretinorum"],
        ["WE0073", "Pais",          "Corporis inscriptionum Latinarum supplementa Italica. Fasciculus I. Pais, Addidamenta ad vol. V Galliae Cisalpinae"],
        ["WE0124", "Pikhaus",       "Pikhaus, Répertoire des inscriptions Latines versifiées de l'Afrique Romaine (Ier – VIe siècles). I. Tripolitaine, Byzacène, Afrique proconsulaire."],
        ["WE0074", "Popescu",       "Popescu, Inscriptiile grecesti si latine din secolele IV–XIII descoperite în România"],
        ["WE0075", "RIB",           "Collingwood; Wright, The Roman inscriptions of Britain"],
        ["WE0096", "RICG",          "Recueil des inscriptions chrétiennes de la Gaule antérieures à la Renaissance carolingienne"],
        ["WE0076", "RIT",           "Alföldy, Die römischen Inschriften von Tarraco"],
        ["WE0077", "RIU",           "Die römischen Inschriften Ungarns. 1. Lieferung (1972): Savaria, Scarbantia und die Limes-Strecke Ad Flexum – Arrabona [n. 1–284]. 2. Lieferung (1976): Salla, Mogentiana, Mursella, Brigetio [n. 285–634]. 3. Lieferung (1981): Brigetio (Fort.) und die Limesstrecke am Donauknie [n. 635–948]. 4. Lieferung (1984): Das Gebiet zw. der Drau u. der Limesstrecke Lussonium – Altinum [n. 949–1050]. 5. Lieferung (1991): Intercisa [n. 1051–1297]. 6. Lieferung (2001): Das Territorium von Aquincum ... [n. 1298–1561b]"],
        ["WE0141", "RIU Suppl.",    "Kovács, Tituli Romani in Hungaria reperti. Supplementum (2005)"],
        ["WE0078", "RMD",           "RMD I = Margaret M. Roxan, Roman military diplomas 1954–1977 (1978) [n. 1–78]. RMD II = Margaret M. Roxan, Roman military diplomas 1978 to 1984 (1985) [n. 79–135]. RMD III = Margaret M. Roxan, Roman military diplomas 1985–1993 (1994) [n. 136–201]. RMD IV = Margaret Roxan; Paul Holder, Roman military diplomas IV (2003) [n. 202–322]. RMD V = Paul Holder, Roman military diplomas V (2006) [n. 323–476]"],
        ["WE0079", "Rugo",          "Rugo, Le iscrizioni dei sec. VI – VII – VIII esistenti in Italia"],
        ["WE0080", "Samothrace",    "Samothrace. Vol. 2, 1: Fraser, The inscriptions on stone"],
        ["WE0142", "Sched. XVIII/1", "Scheden zum Carmina-Band Stadt Rom"],
        ["WE0081", "SEG",           "Supplementum epigraphicum Graecum"],
        ["WE0115", "Segre",         "Segre, Iscrizioni di Cos [red. di Dina Peppas Delmousou ... et al.]"],
        ["WE0084", "Spomenik",      "Spomenik"],
        ["WE0085", "Suppl. It.",    "Supplementa Italica. Nuova serie"],
        ["WE0126", "Suppl. It. — Latium vetus", "Supplementa Italica — Imagines: Supplementi fotografici ai volumi italiani del CIL. Latium vetus (CIL XIV; Eph. Epigr. VII e IX). 1. Latium vetus praeter Ostiam"],
        ["WE0125", "Suppl. It. — Roma", "Supplementa Italica — Imagines: Supplementi fotografici ai volumi italiani del CIL. Roma. 1. Musei Capitolini. 2. Musei Vaticani 1 — Antiquarium Comunale del Celio"],
        ["WE0086", "TAM",           "Tituli Asiae minoris"],
        ["WE0087", "Ternes",        "Ternes, Les inscriptions antiques du Luxembourg, in: Hémecht 17, 1966, H. 3–4"],
        ["WE0088", "Thylander",     "Thylander, Inscriptions du port d’Ostie"],
        ["WE0089", "Vollmer",       "Vollmer, Inscriptiones Baivariae Romanae sive inscriptiones prov. Raetiae adiectis aliquot Noricis Italicisque"],
        ["WE0091", "Wagner",        "Wagner, Neue Inschriften aus Raetien, in: 37.–38. Bericht der Römisch-Germanischen Kommission 1956–1957, 215–264"],
        ["WE0090", "Weber, Suppl. CIL III", "Weber, Supplementum epigraphicum zu CIL III für Salzburg, Steiermark, Oberösterreich und das norische Niederösterreich 1902–1964"],
        ["WE0122", "Zarker",        "Zarker, Studies in the Carmina Latina epigraphica"],
    ];

    static $cil_wes = [
        'WE0010',
        'WE0011',
        'WE0012',
        'WE0013',
        'WE0014'
    ];

    static $table       = 'web_editions';
    static $file_sql    = '../data/cil/output/web_editions.sql';
    static $file_js     = '../data/cil/output/abbreviations.js';



    // -----------------------------------------------------------------------
    public function handle()
    {
        $time = date('U');

        echo(
            "\n\n".
            "----------------------------------------------------------\n".
            "------------------ CIL EDITION IMPORTER ------------------\n".
            "----------------------------------------------------------\n\n"
        );

        // Processing
        $i_cil = $i_others = 0;

        foreach(self::$editions as $row) {

            // Get ID to write and set increment
            if (in_array($row[0], self::$cil_wes)) {
                ++$i_cil;
                $id = 100 + $i_cil;
            }
            else {
                ++$i_others;
                $id = 200 + $i_others;
            }

            // Fix some typical issues
            $row[1] = str_replace('—', '–', $row[1]); // Replace 'Spiegelstriche'

            //----------------------------------------------------------------------------------------------------

            // Report
            echo( "\t". $id ." ". $row[0] ." ". $row[1] ." : ". $row[2] ."\n" );

            // Escape Strings (excluding concordance to allow easy comparison für JS-Apending)
            $row[1] = "'".trim(str_replace("'", "\\'", $row[1]))."'";
            $row[2] = "'".trim(str_replace("'", "\\'", $row[2]))."'";

            // Apend to JS content
            if (!in_array($row[0], self::$cil_wes)) { $js_content [] = '    { k: '.$row[1].', v: '.$row[2].' }';}

            // Escape Concordance
            $row[0] = "'".$row[0]."'";

            // Apend to SQL Content
            $sql_content [] = '('.implode(',', array_merge([$id], $row)).')';
        }

        // Write Files
        self::WriteSQL($sql_content);
        self::WriteJS($js_content);


        // Report Total and Imported
        echo("\nSUCCESS: Editions Array parsed:\n" . "\tCIL: $i_cil\n\tOthers: $i_others\n");

        echo("\nexecution time: ".(date('U') - $time)." sec\n");

        // Regular End of Script -------------------------------------------------------
        die(
            "\n\n".
            "----------------------------------------------------------\n".
            "------------------ REGULAR END OF SCRIPT -----------------\n".
            "----------------------------------------------------------\n\n"
        );
    }


    // ----------------------------------------------------------------------------------------------------------------------------------------
    static function WriteSQL ($content) {

        $table = self::$table;

        file_put_contents(self::$file_sql,
'/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE=\'+01:00\' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE=\'NO_AUTO_VALUE_ON_ZERO\' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `'.$table.'`
--

DROP TABLE IF EXISTS `'.$table.'`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `'.$table.'` (
    `id` int NOT NULL AUTO_INCREMENT,
    `id_we` char(6) NOT NULL,
    `abbreviation` varchar(255) NOT NULL,
    `name_full` text NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `abbreviation_UNIQUE` (`abbreviation`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `'.$table.'`
--

LOCK TABLES `'.$table.'` WRITE;
/*!40000 ALTER TABLE `'.$table.'` DISABLE KEYS */;
INSERT INTO `'.$table.'` VALUES '.
implode(',', $content).
';
/*!40000 ALTER TABLE `'.$table.'` ENABLE KEYS */;
UNLOCK TABLES;'
        );

    }


    static function WriteJS ($content) {

        file_put_contents(self::$file_js,
"export const state = () => ({\n".
"  items: [\n".
    implode(",\n", $content)."\n".
"  ]\n".
"})"
        );

    }

}
