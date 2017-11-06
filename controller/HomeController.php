<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Campaign;
use App\Email;
use App\Subscriber;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $page = 'home';

        $now = Carbon::now();
        $from = Carbon::now()->setTime(00, 00, 00);
        //$dt->subHours(24); $dt->subWeek(); $dt->subMonth(); $dt->subMonths(6); toDateTimeString();  

        //$sent = Email::whereBetween('sent_at', [$from , $now])->where('status', 'sent')->count();
        $subscribers = Subscriber::where('active', 1)->count();
        $subscribersAdded = Subscriber::whereBetween('created_at', [$from , $now])->where('active', 1)->count();
        // $clicks = Email::whereBetween('sent_at', [$from , $now])->where('click', 1)->count();
        // $opened = Email::whereBetween('sent_at', [$from , $now])->where('opened', 1)->count();
        $percentOpened = false;
        $percentClicks = false;
        $clicks = false;
        $opened = false;
        $sent = false;
        // if($sent > 0 && $clicks > 0) $percentClicks = ($clicks / $sent * 100).'%';
        // if($sent > 0 && $opened > 0) $percentOpened = ($opened / $sent * 100).'%';


        $campaigns = Campaign::all();
        foreach ($campaigns as $campaign) {
            $campaign->totalEmails = Email::whereBetween('created_at', [$from , $now])->where('campaign_id', $campaign->id)->count();
            $campaign->sentEmails = Email::whereBetween('sent_at', [$from , $now])->where('campaign_id', $campaign->id)->count();
            $campaign->clicks = Email::whereBetween('sent_at', [$from , $now])->where('campaign_id', $campaign->id)->where('click', 1)->count();
            $campaign->opened = Email::whereBetween('sent_at', [$from , $now])->where('campaign_id', $campaign->id)->where('opened', 1)->count();
        }

        return view('home', compact('page', 'campaigns', 'sent', 'subscribers', 'subscribersAdded', 'clicks', 'opened', 'percentClicks', 'percentOpened'));
    }

    public function getChartData(Request $request)
    {
        if($request->ajax()) {
            $period = $request->period;

            if($period == 'last24h') {
                $hours = ['01','03','05','07','09','11','13','15','17','19','21','23'];
                $result = array();
                $result['clicksTotal'] = 0;
                $result['openedTotal'] = 0;
                foreach ($hours as $key => $hour) {
                    $from = Carbon::now()->setTime($hour, 00, 00);
                    $to = Carbon::now()->setTime($hour, 00, 00)->addHours(2);
                    $openedCount = Email::whereBetween('updated_at', [$from , $to])->where('opened', 1)->count();
                    $clicksCount = Email::whereBetween('updated_at', [$from , $to])->where('click', 1)->count();
                    $result['clicks'][$key]['meta'] = 'Clicks';
                    $result['clicks'][$key]['value'] = $clicksCount; 
                    $result['opened'][$key]['meta'] = 'Opened';  
                    $result['opened'][$key]['value'] = $openedCount; 
                    $result['clicksTotal'] = $result['clicksTotal'] + $clicksCount; 
                    $result['openedTotal'] = $result['openedTotal'] + $openedCount;                                                          
                }
                $res['chart'] = [$result['clicks'] , $result['opened']];
                $res['clicksTotal'] = $result['clicksTotal'];
                $res['openedTotal'] = $result['openedTotal'];
                $res['sentTotal'] = Email::whereBetween('sent_at', [Carbon::now()->subHours(24), Carbon::now()])->where('status', 'sent')->count();
                $res['subscribersAdded'] = Subscriber::whereBetween('created_at', [Carbon::now()->subHours(24) , Carbon::now()])->where('active', 1)->count();
                return $res;
            }

            if($period == 'thisWeek') {
                $result = array();
                $result['clicksTotal'] = 0;
                $result['openedTotal'] = 0;                
                for($i = 0; $i < 7; $i++) {
                    $from = Carbon::now()->startOfWeek()->addDays($i);
                    $to = Carbon::now()->startOfWeek()->addDays($i+1);
                    $openedCount = Email::whereBetween('updated_at', [$from , $to])->where('opened', 1)->count();
                    $clicksCount = Email::whereBetween('updated_at', [$from , $to])->where('click', 1)->count();  
                    $result['clicks'][$i]['meta'] = 'Clicks';
                    $result['clicks'][$i]['value'] = $clicksCount; 
                    $result['opened'][$i]['meta'] = 'Opened';  
                    $result['opened'][$i]['value'] = $openedCount; 
                    $result['clicksTotal'] = $result['clicksTotal'] + $clicksCount; 
                    $result['openedTotal'] = $result['openedTotal'] + $openedCount;                     
                }
                $res['chart'] = [$result['clicks'] , $result['opened']];
                $res['clicksTotal'] = $result['clicksTotal'];
                $res['openedTotal'] = $result['openedTotal'];
                $res['sentTotal'] = Email::whereBetween('sent_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->where('status', 'sent')->count();
                $res['subscribersAdded'] = Subscriber::whereBetween('created_at', [Carbon::now()->startOfWeek() , Carbon::now()->endOfWeek()])->where('active', 1)->count();
                return $res; 
            }

            if($period == 'lastWeek') {
                $result = array();
                $result['clicksTotal'] = 0;
                $result['openedTotal'] = 0;                
                for($i = 0; $i < 7; $i++) {
                    $from = Carbon::now()->startOfWeek()->subWeek(1)->addDays($i);
                    $to = Carbon::now()->startOfWeek()->subWeek(1)->addDays($i+1);
                    $openedCount = Email::whereBetween('updated_at', [$from , $to])->where('opened', 1)->count();
                    $clicksCount = Email::whereBetween('updated_at', [$from , $to])->where('click', 1)->count();  
                    $result['clicks'][$i]['meta'] = 'Clicks';
                    $result['clicks'][$i]['value'] = $clicksCount; 
                    $result['opened'][$i]['meta'] = 'Opened';  
                    $result['opened'][$i]['value'] = $openedCount; 
                    $result['clicksTotal'] = $result['clicksTotal'] + $clicksCount; 
                    $result['openedTotal'] = $result['openedTotal'] + $openedCount;                     
                }
                $res['chart'] = [$result['clicks'] , $result['opened']];
                $res['clicksTotal'] = $result['clicksTotal'];
                $res['openedTotal'] = $result['openedTotal'];
                $res['sentTotal'] = Email::whereBetween('sent_at', [Carbon::now()->startOfWeek()->subWeek(), Carbon::now()->endOfWeek()->subWeek()])->where('status', 'sent')->count();
                $res['subscribersAdded'] = Subscriber::whereBetween('created_at', [Carbon::now()->startOfWeek()->subWeek() , Carbon::now()->endOfWeek()->subWeek()])->where('active', 1)->count();
                return $res; 
            } 

            if($period == 'thisMonth') {
                $result = array();
                $result['clicksTotal'] = 0;
                $result['openedTotal'] = 0;                
                $today = Carbon::now()->day;
                for($i = 0; $i < $today; $i++) {
                    $from = Carbon::now()->startOfMonth()->addDays($i);
                    $to = Carbon::now()->startOfMonth()->addDays($i+1);
                    $openedCount = Email::whereBetween('updated_at', [$from , $to])->where('opened', 1)->count();
                    $clicksCount = Email::whereBetween('updated_at', [$from , $to])->where('click', 1)->count();  
                    $result['clicks'][$i]['meta'] = 'Clicks';
                    $result['clicks'][$i]['value'] = $clicksCount; 
                    $result['opened'][$i]['meta'] = 'Opened';  
                    $result['opened'][$i]['value'] = $openedCount; 
                    $result['clicksTotal'] = $result['clicksTotal'] + $clicksCount; 
                    $result['openedTotal'] = $result['openedTotal'] + $openedCount;                     
                }
                $res['chart'] = [$result['clicks'] , $result['opened']];
                $res['clicksTotal'] = $result['clicksTotal'];
                $res['openedTotal'] = $result['openedTotal'];
                $res['sentTotal'] = Email::whereBetween('sent_at', [Carbon::now()->startOfMonth(), Carbon::now()])->where('status', 'sent')->count();
                $res['subscribersAdded'] = Subscriber::whereBetween('created_at', [Carbon::now()->startOfMonth(), Carbon::now()])->where('active', 1)->count(); 
                return $res; 
            } 

            if($period == 'lastMonth') {
                $result = array();
                $result['clicksTotal'] = 0;
                $result['openedTotal'] = 0;                
                $daysInMonth = Carbon::now()->startOfMonth()->subDays(5)->endOfMonth()->day;
                for($i = 0; $i < $daysInMonth; $i++) {
                    $from = Carbon::now()->startOfMonth()->subDays(5)->startOfMonth()->addDays($i);
                    $to = Carbon::now()->startOfMonth()->subDays(5)->startOfMonth()->addDays($i+1);
                    $openedCount = Email::whereBetween('updated_at', [$from , $to])->where('opened', 1)->count();
                    $clicksCount = Email::whereBetween('updated_at', [$from , $to])->where('click', 1)->count();  
                    $result['clicks'][$i]['meta'] = 'Clicks';
                    $result['clicks'][$i]['value'] = $clicksCount; 
                    $result['opened'][$i]['meta'] = 'Opened';  
                    $result['opened'][$i]['value'] = $openedCount; 
                    $result['clicksTotal'] = $result['clicksTotal'] + $clicksCount; 
                    $result['openedTotal'] = $result['openedTotal'] + $openedCount;                     
                } 
                $res['chart'] = [$result['clicks'] , $result['opened']];
                $res['clicksTotal'] = $result['clicksTotal'];
                $res['openedTotal'] = $result['openedTotal'];
                $res['sentTotal'] = Email::whereBetween('sent_at', [Carbon::now()->startOfMonth()->subDays(5)->startOfMonth(), Carbon::now()->startOfMonth()->subDays(5)->endOfMonth()])->where('status', 'sent')->count();
                $res['subscribersAdded'] = Subscriber::whereBetween('created_at', [Carbon::now()->startOfMonth()->subDays(5)->startOfMonth(), Carbon::now()->startOfMonth()->subDays(5)->endOfMonth()])->where('active', 1)->count(); 
                return $res; 
            }                                                
        }

    }

    public function testWeek()
    {
                $result = array();
                $result['clicksTotal'] = 0;
                $result['openedTotal'] = 0;                
                $daysInMonth = Carbon::now()->startOfMonth()->subDays(5)->endOfMonth()->day;
                echo $daysInMonth; //$data->startOfMonth()->subDays(5)->startOfMonth();
                for($i = 0; $i < $daysInMonth; $i++) {
                    $from = Carbon::now()->startOfMonth()->subDays(5)->startOfMonth()->addDays($i);
                    $to = Carbon::now()->startOfMonth()->subDays(5)->startOfMonth()->addDays($i+1);
                    $openedCount = Email::whereBetween('updated_at', [$from , $to])->where('opened', 1)->count();
                    $clicksCount = Email::whereBetween('updated_at', [$from , $to])->where('click', 1)->count();  
                    $result['clicks'][$i]['meta'] = 'Clicks';
                    $result['clicks'][$i]['value'] = $clicksCount; 
                    $result['opened'][$i]['meta'] = 'Opened';  
                    $result['opened'][$i]['value'] = $openedCount; 
                    $result['clicksTotal'] = $result['clicksTotal'] + $clicksCount; 
                    $result['openedTotal'] = $result['openedTotal'] + $openedCount;                     
                }    
    }
}

