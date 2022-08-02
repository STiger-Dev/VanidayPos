@extends('layouts.guest')
<style type="text/css">
    .qrcode_content {
        display: flex;
        align-items: center;    
        flex-direction: column;
        padding: 100px 0px;
    }
    .qrcode_business_name {
        text-align: center;
    }
    body {
        display: flex;
        justify-content: center;
    }
</style>
<div class="qrcode_content">
    <h1 class="qrcode_business_name">{{$qrcode_info['location_name']}}</h1>
    <img class="qrcode_img" src={{$qrcode_img}}>
</div>