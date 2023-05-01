<?php

namespace Notion\Databases\Properties;

enum NumberFormat: string
{
    case Number = "number";
    case NumberChangeCommas = "number_change_commas";
    case NumberWithCommas = "number_with_commas";
    case Percent = "percent";
    case Dollar = "dollar";
    case CanadianDollar = "canadian_dollar";
    case Euro = "euro";
    case Pound = "pound";
    case Yen = "yen";
    case Ruble = "ruble";
    case Rupee = "rupee";
    case Won = "won";
    case Yuan = "yuan";
    case Real = "real";
    case Lira = "lira";
    case Rupiah = "rupiah";
    case Franc = "franc";
    case HongKongDollar = "hong_kong_dollar";
    case NewZealandDollar = "new_zealand_dollar";
    case Krona = "krona";
    case NorwegianKrone = "norwegian_krone";
    case MexicanPeso = "mexican_peso";
    case Rand = "rand";
    case NewTaiwanDollar = "new_taiwan_dollar";
    case DanishKrone = "danish_krone";
    case Zloty = "zloty";
    case Baht = "baht";
    case Forint = "forint";
    case Koruna = "koruna";
    case Shekel = "shekel";
    case ChileanPeso = "chilean_peso";
    case PhilippinePeso = "philippine_peso";
    case Dirham = "dirham";
    case ColombianPeso = "colombian_peso";
    case Riyal = "riyal";
    case Ringgit = "ringgit";
    case Leu = "leu";
}
