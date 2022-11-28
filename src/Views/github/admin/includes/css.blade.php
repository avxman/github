<style>

    *, *::before, *::after{
        box-sizing: border-box;
        padding: 0;
        margin: 0;
    }

    a, a:hover{
        text-decoration: none;
        transition: all 0.1s;
    }

    a:hover{
        transition: all 0.2s;
    }

    .w-100{
        width: 100%;
        max-width: 100%;
    }

    .w-50{
        width: 50%;
        max-width: 50%;
    }

    html, body{
        height: 100%;
        padding: 0;
        margin: 0;
    }

    body{
        font-family: cursive, sans-serif;
        font-size: 16px;
        font-weight: normal;
    }

    .layout{
        height: 100%;
        display: grid;
        grid-template-rows: 70px 1fr 70px;
    }

    .header, .left, .right, .footer{
        padding: 0 15px;
    }

    .header{
        height: 70px;
        background: #3e67ff;
        color: #b6d50c;
        grid-row-start: 1;
        grid-row-end: 2;
        grid-column-start: 1;
        grid-column-end: 3;
    }

    .header-container{
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .header a{
        color: #fff;
    }

    .header a:hover{
        color: #d9d9d9;
    }

    .header .h-right a{
        padding: 0 10px;
    }

    .main{
        display: flex;
        grid-row-start: 2;
        grid-row-end: 3;
        grid-column-start: 1;
        grid-column-end: 3;
    }

    .left{
        height: 100%;
        width: 25%;
        background: #444;
    }

    .menu-command{
        margin-top: 30px;
    }

    .menu-command ul{
        padding: 0;
        margin: 0;
        list-style: none;
    }

    .parent{
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        cursor: pointer;
        border-bottom: 1px dashed #fff;
        padding: 10px 0;
    }

    .parent:last-child{
        border-bottom: 0;
    }

    .parent a{
        color: #fff;
    }

    .parent > a{
        width: calc(100% - 40px);
        display: flex;
        align-items: center;
        height: 40px;
        font-size: 22px;
    }

    .parent > a:hover,
    .parent > a:hover+.arrow::before,
    .parent ul li > a:hover,
    .parent a:hover
    {
        color: #818181;
    }

    .arrow{
        width: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        height: 40px;
        color: #fff;
    }

    .arrow::before{
        content: '\1F892';
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 30px;
    }

    ul.children{
        display: none;
        flex-wrap: wrap;
        flex-direction: column;
        padding-left: 30px;
        margin: 10px 0;
    }

    .parent.active ul.children{
        display: flex;
    }

    .child .link{
        font-size: 18px;
        margin-bottom: 10px;
        display: block;
    }

    .link.active{
        color: burlywood;
    }

    .right{
        height: 100%;
        width: 75%;
        background: #000;
    }

    .content{
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: overlay;
    }

    .command{
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: center;
        padding: 15px;
        background: brown;
        color: blanchedalmond;
        box-shadow: 0 0 10px 2px #dc1b1b;
        text-align: left;
        word-break: break-all;
        min-width: 450px;
        min-height: 100px;
    }

    .command-line, .command-result{
        width: 100%;
    }

    #form{
        display: flex;
        align-items: center;
        justify-content: center;
        flex-wrap: wrap;
    }

    .input-block{
        display: flex;
        align-items: center;
        justify-content: center;
        flex-wrap: wrap;
        margin-bottom: 15px;
        padding: 0 15px;
    }

    .input-block label{
        width: 100%;
    }

    .input-block input{
        width: 100%;
        height: 30px;
        font-size: 16px;
        padding: 0 10px;
        border: 1px solid #000;
        box-shadow: none;
        outline: 0;
        background: coral;
        color: #195805;
    }

    .input-block input:focus{
        box-shadow: none;
    }

    .input-block input[readonly]{
        cursor: no-drop;
    }

    .input-block input:focus:not([readonly]){
        border-color: #fff;
    }

    .btn-send{
        font-size: 20px;
        padding: 10px 15px;
        border: 0;
        background: red;
        color: #fff;
        cursor: pointer;
        transition: all 0.4s;
    }

    .btn-send:hover{
        background: #cf4545;
        transition: all 0.1s;
    }

    #result{
        background: black;
        margin-top: 15px;
    }

    .result-text{
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 20px auto;
        padding: 20px;
    }

    .result-text > ul{
        list-style: none;
    }

    .line-text::before{
        content: '◾';
        width: 8px;
        height: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: darkorange;
        margin-right: 6px;
        font-size: 10px;
    }

    .footer{
        height: 70px;
        background: #b6d50c;
        margin-top: auto;
        grid-row-start: 3;
        grid-row-end: 4;
        grid-column-start: 1;
        grid-column-end: 3;
    }

    .copyright{
        height: 100%;
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #3e67ff;
    }

</style>
