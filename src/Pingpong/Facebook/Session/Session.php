<?php namespace Pingpong\Facebook\Session;

session_start();

class Session
{
	public function all()
	{
		return $_SESSION;
	}

	public function get($key, $default = null)
	{
		return $this->has($key) ? $this->session[$key] : $default;
	}

	public function put($key, $value)
	{
		$_SESSION[$key] = $value;
 	}

	public function has($key)
	{
		return isset($_SESSION[$key]);
	}

	public function forget($key)
	{
		unset($_SESSION[$key]);
	}

	public function destroy()
	{
		session_destroy();
	}
}