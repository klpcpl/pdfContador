<?php


/**
 * Esta función devuelve el número de páginas de un archivo pdf
 * Tiene que recibir la ubicación y nombre del archivo
 */
class ultilcom{


    public static function getNumPagesPdf($filepath){ 
	
        $fp = @fopen(preg_replace("/\[(.*?)\]/i", "",$filepath),"r"); 
        $max=0; 
        while(!feof($fp)) { 
            $line = fgets($fp,255); 
            if (preg_match('/\/Count [0-9]+/', $line, $matches)){
                 preg_match('/[0-9]+/',$matches[0], $matches2); 
                 if ($max<$matches2[0]) $max=$matches2[0]; 
                } 
            } 
            fclose($fp); if($max==0){ 
                $im = new imagick($filepath); 
                // $max=$im->getNumberImages(); 
            } return 
            $max; 
        }

    public static   function get_format($df) {

            $str = '';
            $str .= ($df->invert == 1) ? ' - ' : '';
         
            if ($df->y > 0) {
                // years
                $str .= ($df->y > 1) ? $df->y . ' Years ' : $df->y . ' Year ';
            } if ($df->m > 0) {
                // month
                $str .= ($df->m > 1) ? $df->m . ' Months ' : $df->m . ' Month ';
            } if ($df->d > 0) {
                // days
                $str .= ($df->d > 1) ? $df->d . ' Days ' : $df->d . ' Day ';
            } if ($df->h > 0) {
                // hours
                $str .= ($df->h > 1) ? $df->h . ' Hours ' : $df->h . ' Hour ';
            } if ($df->i > 0) {
                // minutes
                $str .= ($df->i > 1) ? $df->i . ' Minutes ' : $df->i . ' Minute ';
            } if ($df->s > 0) {
                // seconds
                $str .= ($df->s > 1) ? $df->s . ' Seconds ' : $df->s . ' Second ';
            } 
        
            return $str;
        }

        
        
        
}

?>