<header>
	<!-- Web name in the left -->
	<h2>My Friends</h2>
	<!-- Middle of the header -->
	<nav>
		<a title="Home page" class="<?php echo ($page == 0 ? 'active' : ''); ?>" href="index.php">
			Home
		</a>
		<a title="Friend list" class="<?php echo ($page == 1 ? 'active' : '') ; ?>" href="friendlist.php">
			Friend list
		</a>
		<a title="Add new friend" class="<?php echo ($page == 2 ? 'active' : '') ; ?>" href="friendadd.php">
			Add new friend
		</a>
		<a title="About page" class="<?php echo ($page == 3 ? 'active' : '') ; ?>" href="about.php">
			About
		</a>
	</nav>
	<!-- Right side of the header -->
	<div class="userActions">
		<?php
		if(
			isset($_SESSION["friend_id"]) && isset($_SESSION["friend_email"]) && isset($_SESSION["profile_name"])
		): ?> 
			<h3>Hello, <?php echo $_SESSION["profile_name"]?></h3>
			<a title="Sign up" class="signup <?php echo ($page == 4 ? 'active' : '') ; ?>" href="logout.php">
				<button>Log out</button>
			</a>
		<?php else: ?> 
			<a title="Sign in" class="login <?php echo ($page == 4 ? 'active' : '') ; ?>" href="login.php">
				<button>Log In</button>
			</a>
			<a title="Sign up" class="signup <?php echo ($page == 5 ? 'active' : '') ; ?>" href="signup.php">
				<button>Sign Up</button>
			</a>
		<?php 
			endif;
		?>
	</div>
</header>