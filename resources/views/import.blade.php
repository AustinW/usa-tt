
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Import Athletes</title>

    <!-- Bootstrap core CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" rel="stylesheet">
  </head>

  <body>

    

    <div class="container">
      <div class="card-deck mb-3 text-center">
        <h1>Import Athletes</h1>

        
        <form action="/import" method="post">
            @csrf
            <div class="form-group">
              <div class="custom-file">
                  <input type="file" name="file" class="custom-file-input" id="importFile">
                  <label class="custom-file-label" for="importFile">Choose file</label>
              </div>

              <div>
                  <div class="form-group">
                      <label for="text">Paste</label>
                      <textarea class="form-control" id="text" name="text" rows="3"></textarea>
                  </div>
              </div>

              <div>
                  <button type="submit" class="btn btn-primary mb-2">Import</button>
              </div>
            </div>
            
        </form>
      </div>

      <footer class="pt-4 my-md-5 pt-md-5 border-top">
        &copy; 2018 Austin White
      </footer>
    </div>


    <script src="{{ mix('js/app.js') }}"></script>
  </body>
</html>
