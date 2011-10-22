<?php
	/**
	 *
	 *	BaconPHP is provided under the terms of the zlib/png license. By using this software,
	 *	you agree with the terms of the zlib/png lisence. The license agreement extends to all
	 *	the files within this installation.
	 *
	 *	@copyright Copyright 2011 Luiz Fernando Alves da Silva
	 *
	 *	@license zlib/png license
	 *	This software is provided 'as-is', without any express or
	 *	implied warranty. In no event will the authors be held
	 *	liable for any damages arising from the use of this software.
	 *	
	 *	Permission is granted to anyone to use this software for any purpose,
	 *	including commercial applications, and to alter it and redistribute
	 *	it freely, subject to the following restrictions:
	 *	
	 *	1. The origin of this software must not be misrepresented;
	 *	   you must not claim that you wrote the original software.
	 *	   If you use this software in a product, an acknowledgment
	 *	   in the product documentation would be appreciated but
	 *	   is not required.
	 *	
	 *	2. Altered source versions must be plainly marked as such,
	 *	   and must not be misrepresented as being the original software.
	 *	
	 *	3. This notice may not be removed or altered from any
	 *	   source distribution.
	 *
	 */

	require "core/core.php";

	$url = isset($_GET['action']) ? $_GET['action'] : "";

	$bacon = new Core;
	$bacon->run($url);
?>