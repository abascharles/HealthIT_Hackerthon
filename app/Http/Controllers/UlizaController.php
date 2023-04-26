<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use DB;
use Hash;
use App\User;
use App\UlizaPage;

class UlizaController extends Controller
{

	public function home()
	{
		$page = UlizaPage::where('link', 'home')->first();
		$page2  = UlizaPage::where('link', 'home2')->first();
		return view('uliza.uliza', compact('page', 'page2'));
	}

	public function uliza()
	{
		$page = UlizaPage::where('link', 'uliza')->first();
		return view('uliza.uliza', compact('page'));
	}

	public function ushauri()
	{
		$page = UlizaPage::where('link', 'ushauri')->first();
		return view('uliza.uliza', compact('page'));
	}

	public function trainsmart()
	{
		$page = UlizaPage::where('link', 'trainsmart')->first();
		return view('uliza.uliza', compact('page'));
	}

	public function echo_page()
	{
		$page = UlizaPage::where('link', 'echo')->first();
		return view('uliza.uliza', compact('page'));
	}

	public function faqs()
	{
		$page = UlizaPage::where('link', 'faqs')->first();
		return view('uliza.uliza', compact('page'));
	}

	public function contactus()
	{
		$page = UlizaPage::where('link', 'contactus')->first();
		return view('uliza.uliza', compact('page'));
	}


	public function pages()
	{
		$pages = UlizaPage::all();
		return view('uliza.pages', compact('pages'));		
	}



	public function clinicalform()
	{
		$reasons = DB::table('uliza_reasons')->get();
		$regimens = DB::table('viralregimen')->get();
		return view('uliza.clinicalform', compact('reasons', 'regimens'));
	}


	public function clinical_review()
	{
		$reasons = DB::table('uliza_reasons')->get();
		$regimens = DB::table('viralregimen')->get();
		return view('uliza.clinical_review', compact('reasons', 'regimens'));		
	}
}
