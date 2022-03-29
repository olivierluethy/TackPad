<nav>
    <?php
            if(isset($_SESSION['loggedin']) == true){
                echo "<logo id='logo'></logo>
                <a class='active' href='home'>Home</a>
                <a href='about'>About</a>
                <a href='tackpad'>TackPad</a>
                <a href='logout'>Logout</a>
            </div>";
            }else{
                echo "<logo id='logo'></logo>
                <a class='active' href='home'>Home</a>
                <a href='about'>About</a>
                <a href='tackpad'>TackPad</a>
                <a href='login'>Login</a>
            </div>";
            }?>
</nav>