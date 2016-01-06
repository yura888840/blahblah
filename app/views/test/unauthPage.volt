<h1>Auth Test</h1>
<script>
    $.ajax({
      method: "POST",
      url: "/test/unauthorizedtest401",
      dataType: "json",
      statusCode: parseStatusCode(),
      success: function(data){
          console.log(data);
          checkAuthorizationJson(data);
        }
    });
</script>