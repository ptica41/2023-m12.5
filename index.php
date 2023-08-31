<?php
$example_persons_array = [
    [
        'fullname' => 'Иванов Иван Иванович',
        'job' => 'tester',
    ],
    [
        'fullname' => 'Степанова Наталья Степановна',
        'job' => 'frontend-developer',
    ],
    [
        'fullname' => 'Пащенко Владимир Александрович',
        'job' => 'analyst',
    ],
    [
        'fullname' => 'Громов Александр Иванович',
        'job' => 'fullstack-developer',
    ],
    [
        'fullname' => 'Славин Семён Сергеевич',
        'job' => 'analyst',
    ],
    [
        'fullname' => 'Цой Владимир Антонович',
        'job' => 'frontend-developer',
    ],
    [
        'fullname' => 'Быстрая Юлия Сергеевна',
        'job' => 'PR-manager',
    ],
    [
        'fullname' => 'Шматко Антонина Сергеевна',
        'job' => 'HR-manager',
    ],
    [
        'fullname' => 'аль-Хорезми Мухаммад ибн-Муса',
        'job' => 'analyst',
    ],
    [
        'fullname' => 'Бардо Жаклин Фёдоровна',
        'job' => 'android-developer',
    ],
    [
        'fullname' => 'Шварцнегер Арнольд Густавович',
        'job' => 'babysitter',
    ],
];

function getFullnameFromParts($surname, $name, $patronymic) {
    return "$surname $name $patronymic";
}

function getPartsFromFullname($data) {
    $dataToArr = explode(" ", $data);
    $keys = ['surname' , 'name', 'patronymic'];
    return array_combine($keys, $dataToArr);
}

function getShortName($data) {
    $fullData = getPartsFromFullname($data);
    $sur = mb_substr($fullData['surname'], 0, 1);
    return "{$fullData['name']} $sur.";
}

function getGenderFromName($data) {
    $fullData = getPartsFromFullname($data);
    $sum = 0;
    if (mb_substr($fullData['surname'], -2) === 'ва') {$sum--;} 
    if (mb_substr($fullData['surname'], -1) === 'в') {$sum++;}
    if (mb_substr($fullData['name'], -1) === 'а') {$sum--;}
    if (mb_substr($fullData['name'], -1) === 'й' || $fullData['name'][-1] === 'н') {$sum++;}
    if (mb_substr($fullData['patronymic'], -3) === 'вна') {$sum--;}
    if (mb_substr($fullData['patronymic'], -2) === 'ич') {$sum++;}
    if ($sum > 0) {return 1;}
    elseif ($sum <0) {return -1;}
    else return 0;
}

function getGenderDescription($arr) {
    $male = 0;
    $count = count($arr);
    $filter_male = array_filter($arr, function($i){
        return getGenderFromName($i['fullname']) === 1;
    });
    $filter_female = array_filter($arr, function($i){
        return getGenderFromName($i['fullname']) === -1;
    });
    $filter_unknown = array_filter($arr, function($i){ // функция для наглядности, далее для подсчета статистики я ее не использую
        return getGenderFromName($i['fullname']) === 0;
    });
    $stat_male = round((count($filter_male) / $count) * 100, 2);
    $stat_female = round((count($filter_female) / $count) * 100, 2);
    $stat_unknown = 100 - $stat_female - $stat_male;
    $stat = <<<HEREDOC
    Гендерный состав аудитории:
    ---------------------------
    Мужчины - $stat_male%
    Женщины - $stat_female%
    Не удалось определить - $stat_unknown%
    HEREDOC;
    return $stat;
}

function getPerfectPartner($surname, $name, $patronymic, $arr) {
    $surname = mb_convert_case($surname, MB_CASE_TITLE_SIMPLE);
    $name = mb_convert_case($name, MB_CASE_TITLE_SIMPLE);
    $patronymic = mb_convert_case($patronymic, MB_CASE_TITLE_SIMPLE);
    $strArg = getFullnameFromParts($surname, $name, $patronymic);
    $genderArg = getGenderFromName($strArg);
    if ($genderArg === 0) {return 'Не удалось вернуть пару';}
    $strArg = getShortName($strArg);
    do {
    $strRand = $arr[array_rand($arr)]['fullname'];
    $genderRand = getGenderFromName($strRand);
    } while (abs($genderArg - $genderRand) <= 1);
    $strRand = getShortName($strRand);
    $rand = rand(5000, 10000) / 100;
    $output = <<<HEREDOC
    $strArg + $strRand = 
    ♡ Идеально на $rand% ♡
    HEREDOC;
    return $output;
}

print(getPerfectPartner('Иванов', 'ивАн', 'Иванович', $example_persons_array));