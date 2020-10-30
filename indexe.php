<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>

<style>

    * {
        box-sizing: border-box;
    }

    .lista {
        height: 300px;
        overflow: scroll;
        width: 50%;
        margin: auto;
        border: 3px solid #2a6193;
    }
    dl > div {
        background: #FFF;
        padding: 24px 0 0 0;
    }

    
    dt {
        background: #B8C1C8;
        border-bottom: 1px solid #989EA4;
        border-top: 1px solid #717D85;
        color: #FFF;
        font: bold 18px/21px Helvetica, Arial, sans-serif;
        margin: 0;
        padding: 2px 0 0 12px;
        position: -webkit-sticky;
        position: sticky;
        top: 0px;
    }

    dd {
        font: bold 20px/45px Helvetica, Arial, sans-serif;
        margin: 0;
        padding: 0 0 0 12px;
        white-space: nowrap;
    }
      
    dd + dd {
        border-top: 1px solid #CCC;
    }

</style>

</head>
<body>
    <h1>
        Lista
    </h1>

    <div class="lista">  

        <dl>
            <div>
                <dt>A</dt>
                <dd>Andrew W.K.</dd>
                <dd>Apparat</dd>
                <dd>Arcade Fire</dd>
                <dd>At The Drive-In</dd>
                <dd>Aziz Ansari</dd>
            </div>
            <div>
                <dt>C</dt>
                <dd>Chromeo</dd>
                <dd>Common</dd>
                <dd>Converge</dd>
                <dd>Crystal Castles</dd>
                <dd>Cursive</dd>
            </div>
            <div>
                <dt>E</dt>
                <dd>Explosions In The Sky</dd>
            </div>
            <div>
                <dt>T</dt>
                <dd>Ted Leo &amp; The Pharmacists</dd>
                <dd>T-Pain</dd>
                <dd>Thrice</dd>
                <dd>TV On The Radio</dd>
                <dd>Two Gallants</dd>
            </div>
        </dl>

    </div>
    
    <div class="info">  
        <?php phpinfo(); ?>
    </div>
    
</body>
</html>