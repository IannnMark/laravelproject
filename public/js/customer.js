$(document).ready(function () {
    $("#ctable").DataTable({
        ajax: {
            url: "/api/customers/all",
            dataSrc: "",
        },
        dom: '<"top"<"left-col"B><"center-col"l><"right-col"f>>rtip',
        buttons: [
            {
                extend: "pdf",
                className: "btn btn-success glyphicon glyphicon-file",
            },
            {
                extend: "excel",
                className: "btn btn-success glyphicon glyphicon-list-alt",
            },
             {
                text: "Add Customer",
                className: "btn btn-success",
                action: function (e, dt, node, config) {
                    $("#cform").trigger("reset");
                    $("#customerModal").modal("show");
                },
            },
        
        ],
        columns: [
            {
                data: "id",
            },
            {
                data: "fname",
            },
            {
                data: "lname",
            },

            {
                data: "address",
            },
            {
                data: "town",
            },
            {
                data: "city",
            },
             {
                data: "phone",
            },
            {
                data: null,
                render: function (data, type, row) {
                    console.log(data.customer_image)
                    return `<img src= "storage/${data.customer_image}" "height="90px" width="90px">`;
                },
            },
              {
                data: null,
                render: function (data, type, row) {
                    return "<a href='#' class='editBtn' id='editbtn' data-id=" +
                        data.id +
                        "><i class='fa-solid fa-pen' aria-hidden='true' style='font-size:24px' ></i></a><a href='#' class='deletebtn' data-id=" + data.id + "><i class='fa-solid fa-trash-can' style='font-size:24px; color:red; margin-left:15px;'></a></i>";
                },
            },
        ],
    });

//stire
$("#customerSubmit").on("click", function (e) {
        
    e.preventDefault();
    var data = $("#cform")[0];
    console.log(data);
    let formData = new FormData(data);
    console.log(formData);
    for (var pair of formData.entries()) {
        console.log(pair[0] + "," + pair[1]);
    }

    $.ajax({
        type: "POST",
        url: "/api/register",
        data: formData,
        contentType: false,
        processData: false,
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        dataType: "json",
        success: function (data) {
            console.log(data);
            var $ctable = $("#ctable").DataTable();
            $ctable.row.add(data.customer).draw(false);

            window.localStorage.setItem(data, 'MyApp');

            bootbox.alert("Successfully Registered You can now Login to the web page!", function() {
                location.replace('/');
            });
        },
        error: function (error) {
            console.log(error);
        },
    });
});


//EDIT
$("#ctable tbody").on("click", "a.editBtn", function (e) {
    e.preventDefault();
    $("#customerModal").modal("show");
    var id = $(this).data("id");

    $.ajax({
        type: "GET",
        enctype: "multipart/form-data",
        processData: false,
        contentType: false,
        cache: false,
        url: "/api/customers/" + id + "/edit",
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        dataType: "json",
        success: function (data) {
            console.log(data);
               $("#customer_id").val(data.id);
               $("#fname").val(data.fname);
               $("#lname").val(data.lname);
               $("#address").val(data.address);
               $("#town").val(data.town);
               $("#city").val(data.city);
               $("#phone").val(data.phone);
        },
        error: function (error) {
            console.log("error");
        },
    });
});


//CUSTOMER UPDATE
$("#customerUpdate").on("click", function (e) {
    e.preventDefault();
    var id = $("#customer_id").val();
    var data = $("#cform")[0];
    let formData = new FormData(data);
    console.log(formData);
    for (var pair of formData.entries()) {
        console.log(pair[0] + "," + pair[1]);
    }
    var table = $("#ctable").DataTable();
    console.log(id);

    $.ajax({
        type: "POST",
        url: "/api/customers/" + id,
        data: formData,
        contentType: false,
        processData: false,
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        dataType: "json",
        success: function (data) {
            console.log(data);
            $("#customerModal").modal("hide");
            table.ajax.reload();
        },
        error: function (error) {
            console.log(error);
        },
    });
});


//DELETE
$("#cbody").on("click", ".deletebtn", function (e) {
    var id = $(this).data("id");
    var $tr = $(this).closest("tr");
    // var id = $(e.relatedTarget).attr('id');
    console.log(id);
    e.preventDefault();
    bootbox.confirm({
        message: "Do you want to delete this customer",
        buttons: {
            confirm: {
                label: "Yes",
                className: "btn-success",
            },
            cancel: {
                label: "No",
                className: "btn-danger",
            },
        },
        callback: function (result) {
            if (result)
                $.ajax({
                    type: "DELETE",
                    url: "/api/customers/" + id,
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                            "content"
                        ),
                    },
                    dataType: "json",
                    success: function (data) {
                        console.log(data);
                        
                        $tr.find("td").css('backgroundColor','hsl(0,100%,50%').fadeOut(2000, function () {
                            $tr.remove();
                        });
                        
                        
                    },
                    error: function (error) {
                        console.log(error);
                    },
                });
        },
    });
});


});
