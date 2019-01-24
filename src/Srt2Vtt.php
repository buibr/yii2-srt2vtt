<?php

namespace buibr\subtitles;

/**
 * This is just an example.
 */
class Srt2Vtt extends \yii\base\Widget
{
    /**
     *  SRT FILE
     */
    private $srt;

    /**
     * VTT FILE
     */
    private $vtt;   // output.

    /**
     * Handler of fopen
     */
    private $handler;

    /**
     * @param string $srt - path of the srt file as input
     * @param string $vtt - Vtt gilr 
     */
    public function __construct($srt=null, $vtt=null)
	{
		$this->srt  = $this->setSrt($srt);
		$this->vtt  = $this->setVtt($vtt);
    }
    
    /**
     * @param string $srt
     */
    public function setSrt($srt)
    {
        if(\is_writable($srt))
        {
            return $this->srt = $srt;
        }

        throw new \ErrorException("Srt file is not found");
    }

    /**
     * @param string $srt
     */
    public function setVtt($vtt)
    {
        $att = pathinfo($vtt);

        if($att['dirname'] == '.')
        {
            return $this->vtt = $vtt;
        }

        if(\is_dir($att['dirname']) )
        {
            return $this->vtt = $vtt;
        }

        throw new \ErrorException("Vtt file is not accessible");
    }

    /**
 	 * Convert srt to vtt
 	 *
 	 * @return void
	**/
	public function convert()
	{
		// Read the srt file content into an array of lines
		$this->handler = fopen($this->srt, 'r');

		if($this->handler) {
			//	Assume that every line has maximum 8192 length
			//	If you don't care about line length then you can omit the 8192 param
			$lines = array();

			//
			while (($line = fgets($this->handler, 8192)) !== false):
				$lines[] = $line;
			endwhile;

            if (!feof($this->handler)) 
                throw new \ErrorException("Error: unexpected fgets() fail.");
			else ($this->handler);
		}
		// Convert all timestamp lines
		// The first timestamp line is 1
		$length = count($lines);

		for ($index = 1; $index < $length; $index++)
		{
			// A line is a timestamp line if the second line above it is an empty line
			if ($index === 1 || trim($lines[$index - 2]) === '')
			{
				$lines[$index] = str_replace(',', '.', $lines[$index]);
			}
        }
        
        /**
         * 
         */
        $content = implode('', $lines);
        
        /**
         * 
         */
        $content = utf8_encode($content);

        /**
         * 
         */
        $content = str_replace('ï»¿', '', $content);

        /**
         * 
         */
		return \file_put_contents($this->vtt, "WEBVTT\n\n" .$content);
	}

    
}
