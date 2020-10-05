<?php

date_default_timezone_set('Europe/Moscow');
include_once ($_SERVER['DOCUMENT_ROOT'] . '/class/db.php');
$dal = new DAL();
class Schedule {

    public static function days()
    {
        return ["Понедельник", "Вторник",
            "Среда", "Четверг",
            "Пятница", "Суббота",
            "Воскресенье"];
    }

    public static function pair_type($type)
    {
        if($type == 'practice') return 'Практическая работа';
        else if($type == 'lecture') return 'Лекция';
        else if($type == 'lab') return 'Лабараторная работа';
        else return 'Семинар';
    }

    public static function date_day()
    {
        $week_day = Schedule::days()
            [intval(date('N')) - 1];
        $day = intval(date('d'));
        $months = [
            "Января", "Ферваля",
            "Марта", "Апреля",
            "Мая", "Июня",
            "Июля", "Августа",
            "Сентября", "Октября",
            "Ноября", "Декабря"];
        $month = intval(date('n')) - 1;
        $month = $months[$month];
        $week_cnt = intval(date('W')) % 2 == 0 ? 'ч/н' : 'н/н';
        return "$week_day, $day $month ($week_cnt)";
    }
    
    public static function date_today()
    {
        $week_day = Schedule::days()
            [intval(date('N')) - 1];
        $day = intval(date('d'));
        $months = [
            "Января", "Ферваля",
            "Марта", "Апреля",
            "Мая", "Июня",
            "Июля", "Августа",
            "Сентября", "Октября",
            "Ноября", "Декабря"];
        $month = intval(date('n')) - 1;
        $month = $months[$month];
        $week_cnt = intval(date('W')) % 2 == 0 ? 'ч/н' : 'н/н';
        return "$week_day, $day $month ($week_cnt)";
    }
    
    public static function today($role, $role_id)
    {
        $today = intval(date('N'));
        $week = intval(date('W')) % 2 == 0 ? 'even' : 'odd';
        return Schedule::daily($today, $week, $role, $role_id);
    }
    
    public static function parity()
    {
        return intval(date('W')) % 2 == 0;
    }
    
    /* week: even, odd */
    public static function daily($day, $week, $role, $role_id)
    {
        $dal = new DAL();
        $day = intval($day);
        $role_id = intval($role_id);
        $ws = "('$week', 'both')";
        if ($week == 'both')
            $ws = "('odd', 'even', 'both')";
        if($role == 'teacher') $schedule = $dal->daily_t($day, $role_id, $ws);
        else $schedule = $dal->daily_g($day, $role_id, $ws);
        return $schedule;
    }
    
    public static function weekly($week, $role, $role_id)
    {
        $dal = new DAL();
        $role_id = intval($role_id);
        $ws = "('$week', 'both')";
        if ($week == 'both')
            $ws = "('odd', 'even', 'both')";
        if($role == 'teacher') $schedule = $dal->weekly_t($role_id, $ws);
        else $schedule = $dal->weekly_g($role_id, $ws);
        return isset($schedule['id']) ? [$schedule] : $schedule;
    }
    
    public static function pair_time($pair)
    {
        switch ($pair) {
            case 1: return '07:45-09:20'; break;
            case 2: return '09:40-11:15'; break;
            case 3: return '11:35-13:10'; break;
            case 4: return '13:40-15:15'; break;
            case 5: return '15:35-17:10'; break;
            case 6: return '17:30-19:05'; break;
            
            case '(Перемена) 1': return '9:20-9:40'; break;
            case '(Перемена) 2': return '11:15-11:35'; break;
            case '(Перемена) 3': return '13:10-13:40'; break;
            case '(Перемена) 4': return '15:15-15:35'; break;
            case '(Перемена) 5': return '17:10-17:30'; break;
            case '(Перемена) 6': return '19:05-19:30'; break;
        }
    }

    public static function current_pair()
    {
        $L = [465, 555, 580, 670, 695, 785, 820, 910, 935, 1025, 1050, 1140];
        $C = date('H') * 60 + date('i');
        for ($i = 1, $k = $i; $i <= 6; $i++, $k = $k+2)
        {
            if ($C >= $L[$k - 1] and $C <= $L[$k])
                return $i;
            else if ($C >= $L[$k] and $C <= $L[$k+1])
                return '(Перемена) ' . $i;
        }        
        return 0;
    }
    /*
    public static function current_pair()
    {
        $L = [465, 580, 695, 820, 935, 1050];
        $C = date('H') * 60 + date('i');
        for ($i = 1; $i < 6; $i++)
        {
            if ($C >= $L[$i - 1] and $C <= $L[$i])
                return $i;
        }        
        return 0;
    }
    */
}
?>

