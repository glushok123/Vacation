<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Event;
use Redirect,Response;


class FullCalendarController extends Controller
{
  public function index()
  {
      if(request()->ajax())
      {

       $start = (!empty($_GET["start"])) ? ($_GET["start"]) : ('');
       $end = (!empty($_GET["end"])) ? ($_GET["end"]) : ('');

       $data = Event::whereDate('start', '>=', $start)->whereDate('end',   '<=', $end)->get(['id','title','start', 'end', 'user', 'ytv']);

       return Response::json($data);

      }
      return view('home');
  }


  public function create(Request $request)
  {
      $insertArr = [ 'title' => $request->title,
                     'start' => $request->start,
                     'user' => $request->user,
                     'end' => $request->end
                  ];
      $event = Event::insert($insertArr);
      return Response::json($event);
  }


  public function update(Request $request)
  {
      $where = array('id' => $request->id);
      $updateArr = ['title' => $request->title,'start' => $request->start, 'end' => $request->end, 'ytv' => $request->ytv];
      $event  = Event::where($where)->update($updateArr);
      return Response::json($event);
  }


  public function destroy(Request $request)
  {
      $event = Event::where('id',$request->id)->delete();

      return Response::json($event);
  }
}
