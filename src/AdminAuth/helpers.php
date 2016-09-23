<?php

if (! function_exists('admin') )
{
	/**
	 * Returns the admin session instance
	 *
	 * @return  mixed
	 */
	function admin()
	{
		return auth()->guard('admin')->user();
	}
}

if (! function_exists('isAdminLoggedIn') )
{
	/**
	 * Determines if admin is logged in.
	 *
	 * @return boolean  True if admin logged in, False otherwise.
	 */
	function isAdminLoggedIn()
	{
		return auth()->guard('admin')->check();
	}
}