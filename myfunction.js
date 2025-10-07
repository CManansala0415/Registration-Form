
function test(){
    $.ajax({
        url: "crud.php",
        type: 'GET',
        data: {

        },
        success: function(res) {
            console.log(res);
        }
    });
}

