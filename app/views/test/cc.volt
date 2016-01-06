<script type="text/javascript">
    var evtSource = new EventSource("/sse", {withCredentials: true});

    evtSource.addEventListener("message", function (e) {

        console.log(JSON.parse(e.data));

    }, false);
</script>