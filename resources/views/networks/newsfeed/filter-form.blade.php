{!! Form::open([ 'url' => 'network', 'id'=>'frmfeedSearch', 'method' => 'get' ]) !!}
{!! Form::hidden('q','search') !!}
<div class="col-md-12 padding-none inner-form">
   <div class="col-md-4 form-control-fld padding-left-none">
      <div class="input-prepend">
         <span class="add-on"><i class="fa fa-search"></i></i></span>
         {!! Form::text('fs', request('fs'), 
               ['class' => 'form-control', 'id'=>'txtFeedSearch', 'placeholder'=>'Search']
             ) 
         !!}
      </div>
   </div>
   <div class="col-md-3 form-control-fld padding-left-none">
      <div class="normal-select">
         {{--*/
            $feedTypeArr = array( '' => 'Select Type', 
               'feed' => 'Feed', 'job' => 'Job', 'article' =>  'Article', 
               'recomend' => 'Recommendations', 'partner' => 'Partner', 'follower' => 'Follower',
               'group' => 'Groups'
            )
         /*--}}
         {!! Form::select('ft', $feedTypeArr, request('ft'), ['class' => 'selectpicker']) !!}
      </div>
   </div>
   <div class="col-md-2 form-control-fld padding-left-none">
      <div class="input-prepend">
         <span class="add-on"><i class="fa fa-calendar-o"></i></span>
         {!! Form::text('fdd',request('fdd'), 
               ['class' => 'form-control calendar', 'id'=>'txtFdDtFrm', 'placeholder'=>'From Date', 
               'readonly' => 'readonly']
             ) 
         !!}
      </div>
   </div>
   <div class="col-md-2 form-control-fld padding-left-none">
      <div class="input-prepend">
         <span class="add-on"><i class="fa fa-calendar-o"></i></span>
         {!! Form::text('fdt',request('fdt'), 
               ['class' => 'form-control calendar', 'id'=>'txtFdDtTo', 'placeholder'=>'To Date', 'readonly' => 'readonly']
             ) 
         !!}
      </div>
   </div>
   <div class="col-md-1 form-control-fld padding-none">
      <div class="input-prepend">
         <button type="submit" id="btnSeaPostFeed" class="btn add-btn block-btn">Go</button>
      </div>
   </div>
</div>
{!! Form::close() !!}


