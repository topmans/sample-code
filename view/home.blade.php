@extends('layouts.app')

@section('page-scripts')
<script src="/js/chartist.min.js"></script>
<script src="/js/chartist-plugin-tooltip.js"></script>
<script src="/js/dashboard.js"></script>
@endsection

@section('page-css')
<link href="/css/chartist.css" rel="stylesheet">
@endsection

@section('content')
<div class="container dashboard-head">
  <div class="row">
    <div class="col-md-12">
      <div class="panel panel-default top-panel">
        <div class="panel-heading">
          <div class="row">
            <div class="col-md-9 col-sm-9 col-xs-6">
              <h4><strong>Dashboard</strong></h4>
            </div>
            <div class="col-md-3 col-sm-3 col-xs-6">
              <select class="form-control pull-right" id="selectPeriod">
                <option value="last24h">Today</option>
                <option value="thisWeek">This week</option>
                <option value="lastWeek">Last week</option>
                <option value="thisMonth">This month</option>
                <option value="lastMonth">Last month</option>
              </select>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="container dashboard">
  <div class="row">
    <div class="col-md-3">
      <div class="panel panel-default">
        <div class="panel-body">
          <span class="value">{{$subscribers}}</span> <span class="plus-value pull-right" id="subscribersAddedCount">+{{$subscribersAdded}}</span><br />
          <span class="text">Subscribers</span> <span class="text pull-right"> added</span>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="panel panel-default ">
        <div class="panel-body">
          <span class="value" id="emailSentCount">{{$sent}}</span><br />
          <span class="text">Email sent</span>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="panel panel-default">
        <div class="panel-body">
          <span class="value" id="openedCount">{{$opened}}</span> <span class="plus-value pull-right">{{$percentOpened}}</span><br />
          <span class="text">Opened emails</span>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="panel panel-default">
        <div class="panel-body">
          <span class="value" id="clicksCount">{{$clicks}}</span> <span class="plus-value pull-right">{{$percentClicks}}</span><br />
          <span class="text">Clicks from email</span>
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-md-12">
      <div class="panel panel-default">
        <div class="panel-body">
          <h3>STATS</h3>
          <div  id="main-chart" class="ct-chart"><div class="loader"></div></div> <!--  -->
        </div>
      </div>

      <div class="panel campaigns panel-default">
        <div class="panel-body">
          <h3>CAMPAIGNS</h3>
          @foreach($campaigns as $campaign)
            <div class="row campaign">
              <div class="col-md-1"><span class="circle"></span></div>
              <div class="col-md-4 no-pd">
                <strong class="campaign-name">{{ $campaign->title }}</strong><br />
                <span class="campaign-type">{{ $campaign->type }} campaign</span><br />
                <span class="created">{{ $campaign->created_at->diffForHumans() }} </span>
              </div>
              <div class="col-md-1"><span class="glyphicon glyphicon-chevron-right"></span></div>
              <div class="col-md-2 stat"><strong>{{$campaign->sentEmails}}</strong> <span class="grey-text">of {{$campaign->totalEmails}}</span><br /> Sent </div>
              <div class="col-md-2 stat"><strong>{{$campaign->opened}}</strong><br />opened</div>
              <div class="col-md-2 stat"><strong>{{$campaign->clicks}}</strong> <br />clicked</div>
            </div>
          @endforeach          
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
