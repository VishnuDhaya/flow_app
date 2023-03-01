<head>
</head>
<body>
   <b>Below english words/sentences is missing translation to <span style="text-transform: uppercase">{{$data['default_lang']}}</span> ({{$data['country_code']}} Language)</b>
      <ul>
         @foreach($data['missing_keys'] as $missing => $values)
            <li>
                {{$values}}
            </li>
          @endforeach
      </ul>
</body>