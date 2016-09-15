<div class="col-md-12 padding-none">

                @if($docCount>0)                                     
                <div class="col-md-12 padding-none">
                    <h3>List of documents </h3> 
                    <ul class="popup-list">                                               

                        @foreach($docs_buyer as $doc)
                        <li>{{$doc}}</li>
                        @endforeach

                    </ul>
                </div>
               @else
               No Documents Found
               @endif

        </div>
        