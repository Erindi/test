<?php

class View
{
	function generate(string $viewFileName, array $data)
	{
        if(is_array($data) && !empty($data)) {

            extract($data);
        }

		include '../app/view/'. $viewFileName;
	}
}
