<?php

/**
 * @author     Deepankar Chopra <deepankar.chopra@gmail.com>
 * @version    0.1
 * @link       https://github.com/deepankarchopra/winePuzzle
 * @since      File available since Release 0.1
 */

/* Uncomment these for low configuration machines
ini_set('memory_limit', -1);
set_time_limit(0);
*/

class Puzzle{
	/**
     * Class Puzzle
     *
     * input the file name in class to process
     *
     * @var $fileName
     */
	public $fileName;

	function __construct($text){
		$this->fileName = $text;
	}

	/**
     * function assignWines
     *
     * input the file which contain the person and wine data, output a TSV file which contain the desired result based on puzzle.
     *
     */
	public function assignWines()
	{
		$wineWishlist	= [];
		$wineList 		= [];
		$wineSold 		= 0;
		$finalList 		= [];
	
		//read data from file	
		$lines = file($this->fileName,FILE_SKIP_EMPTY_LINES);
		$allData = implode(',', $lines);
	
		$offset = 0;
		$wineIndex = 0;

		foreach ($lines as $key => $value) 
		{
			$currentElement = explode("\t", $value);
			
			$person = trim($currentElement[0]);

			$wine = trim($currentElement[1]);

			$offset += strlen($value);
			
			$pos = strpos($allData, $wine,$offset);
			
			if ($pos === false && (!isset($finalList[$person]) || count($finalList[$person])<3 ) && !in_array($wine, $wineList) ) //no one else wants same wine and person has already not bought 3 wines
			{
				$wineSold++;
				$finalList[$person][] =  $wine;
				$wineList[$wineIndex++] = $wine;
			}
			else if(!in_array($wine, $wineList))//multiple people want wine, don't allow same wine sold again
			{
				$toFind = $person."\t";

				$pos = strpos($allData,$toFind ,$offset);

				if ($pos === false && (!isset($finalList[$person]) || count($finalList[$person])<3 ) ) //this person wants this wine ,does not want any other wine except the 2 or less wines  he has already bought
				{
					$wineSold++;
					$finalList[$person][] =  $wine;
					$wineList[$wineIndex++] = $wine;
				}
				else if(!isset($finalList[$person]) || count($finalList[$person])<3)//only allow to buy if he has not purchased 3 wines
				{
					$wineSold++;
					$finalList[$person][] =  $wine;
					$wineList[$wineIndex++] = $wine;	
				}				
			}
		}
		
		//write final outputt to file
		$fh = fopen("finalAssign.txt", "w");
		fwrite($fh, "Total number of wine bottles sold in aggregate : ".$wineSold."\n");
		foreach (array_keys($finalList) as $key1 => $person) 
		{
			foreach ($finalList[$person] as $key => $wine) 
			{
				fwrite($fh, $person." ".$wine."\n");
			}
		}
		fclose($fh);
	}
}

$puzzle = new Puzzle("person_wine_3.txt");
$puzzle->assignWines();
echo "Done";
?>
