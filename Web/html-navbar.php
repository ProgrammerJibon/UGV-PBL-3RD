<div class="top" style="background: url('./images.php?img=logo');background-size: contain;">
    <div style="backdrop-filter: blur(1000px);">
    <div class="flex-between">
            <div class="top-logo">
                <img src="./images.php?img=logo" preload alt="logo"/>
            </div>
            <!-- <div class="top-logout-btn">
                <span>Logout</span>
            </div> -->
        </div>
        <nav>
            <a data-href="/home" class="nav-item">
                <span>Home</span>
            </a>
            <a data-href="/order-history" class="nav-item">
                <span>Order History</span>
            </a>
            <a data-href="/students" class="nav-item">
                <span>Students</span>
            </a>
            <!-- <a data-href="/tables" class="nav-item">
                <span>Tables</span>
            </a> -->
            <a data-href="/groups" class="nav-item">
                <span>Item Groups</span>
            </a>
            <a data-href="/items" class="nav-item">
                <span>Items</span>
            </a>
            <a data-href="/logout" class="nav-item">
                <span><font color="red">Logout</font></span>
            </a>
            <!-- <a data-href="/settings" class="nav-item">
                <span>Settings</span>
            </a> -->
        </nav>
    </div>
</div>
<script>
document.querySelectorAll("a").forEach(item=>{
    if(item.dataset.href){
        item.href = item.dataset.href;
        if(item.dataset.href == window.location.pathname){
            item.classList.add("active");        
        }
    }    
})
</script>