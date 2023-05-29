const url = '../api/api_user.php';
var data = [];

function readAllUsers(){
    axios({
        method:'GET',
        url:url,
        responseType:'json'
        }).then(res =>{
            console.log(res.data);
            this.data = res.data.data;
            console.log(res.data.status);
            if((res.data.status=="error"))
                window.location.href = "401.html";
            else
                llenarTabla(data);            
        }).catch(error=>{
                console.error(error);
        });
}

function llenarTabla(data)
{
    document.querySelector('#table-user tbody').innerHTML = '';
    for(let i=0;i<data.length;i++){
        document.querySelector('#table-user tbody').innerHTML += 
        `<tr>
            <td>${data[i].relojes_id}</td>
            <td>${data[i].user_name}</td>
            <td>${data[i].user_last_name}</td>
            <td>${data[i].user_email}</td>
            <td><button type="button" onclick="deleteUser(${data[i].user_id})">Delete</button>
            <button type="button" onclick="updateUser(${data[i].user_id})">Update</button> 
            <button type="button" onclick="readUserById(${data[i].user_id})">Read</button> </td>
        </tr>`
    }
}

function deleteUser(id_del){
    let user = { 
        id : id_del };
    axios({
        method:'DELETE',
        url:url,
        responseType:'json',
        data: user
        }).then(res =>{
            console.log(res.data);
            readAllUsers();        
        }).catch(error=>{
            console.error(error);
        });        
}

function createUser(){
let user = { 
    name : document.getElementById('name').value,
    lastname : document.getElementById('lastname').value,
    email : document.getElementById('email').value,
    password : document.getElementById('password').value
    };

    axios({
        method:'POST',
        url:url,
        responseType:'json',
        data: user
        }).then(res =>{
            console.log(res.data);
            if(res.data.message==='Duplicate data')
                alert('Dato duplicado.');
            else
                readAllUsers();        
        }).catch(error=>{
            console.error(error);
        });
}

function updateUser(id_update){
    name_update = document.getElementById('name').value;

    if ( name_update!= "")
    {
    
    let user = { 
        id : id_update,
        name : name_update,
        lastname : document.getElementById('lastname').value,
        email : document.getElementById('email').value,
        password : document.getElementById('password').value
        };

    axios({
        method:'PUT',
        url:url,
        responseType:'json',
        data: user
        }).then(res =>{
            console.log(res.data);
            if(res.data.status==='error')
            alert('Dato duplicado.');
        else
            readAllUsers();         
        }).catch(error=>{
            console.error(error);
        });
    }
    else
    alert("Debe colocar un nombre")

}

function readUserById(id){
    axios({
        method:'GET',
        url:url + '?id='+ id,
        responseType:'json'
        }).then(res =>{
            console.log(res.data);
            document.getElementById('name').value = res.data.data[0].user_name;
            document.getElementById('lastname').value = res.data.data[0].user_last_name;
            document.getElementById('email').value = res.data.data[0].user_email;
        }).catch(error=>{
            console.error(error);
        });
}