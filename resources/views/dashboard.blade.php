<div class="be-content contenido" style="height: 100vh;">
		<div class="main-content container-fluid">






				<div class='container'>

					<div class="col-xs-12 col-sm-offset-0 col-sm-4 col-md-3 col-lg-3 col-md-offset-3">
						<article class="profile">
							<div class="profile-image">
								<span class="mdi mdi-balance" 
				                        	style="font-size: 80px; color: #fff; padding-top: 15px;"></span>
							</div>
							<h2 class="profile-username">{{Session::get('institucion')->codigo}}</h2>
							<small class="profile-user-handle color_azulclaro">CODIGO LOCAL</small>
							<h2 class="profile-username">{{strtoupper(Session::get('institucion')->nombre)}}</h2>
							<small class="profile-user-handle color_azulclaro">{{strtoupper(Session::get('institucion')->departamento)}} | 
						                  			{{strtoupper(Session::get('institucion')->provincia)}} | 
						                  			{{strtoupper(Session::get('institucion')->distrito)}}</small>

						</article>
					</div>
					<div class="col-xs-12 col-sm-4 col-md-3 col-lg-3">
						<article class="profile">
							<div class="profile-image">
				                   <span class="mdi mdi-account" 
				                        	style="font-size: 80px; color: #fff; padding-top: 15px;"></span>
							</div>
							<h2 class="profile-username">{{strtoupper(Session::get('direccion')->dni)}}</h2>
							<small class="profile-user-handle color_azulclaro">DNI</small>
							<h2 class="profile-username">{{strtoupper(Session::get('direccion')->nombres)}}</h2>
							<small class="profile-user-handle color_azulclaro">NOMBRES</small>

						</article>
					</div>

				</div>


				<div class='container' style="margin-top: 10px;">
					<div class="col-xs-12 col-sm-offset-0 col-sm-4 col-md-3 col-lg-3 col-md-offset-3">
						<article class="profile-2">
							<div class="titulo-profile">
								CONEI
							</div>
							<h2 class="profile-username">2024-2025</h2>
							<h2 class="profile-username"><span class="label label-warning">PROCESO</span></h2>
						</article>
					</div>
					<div class="col-xs-12 col-sm-4 col-md-3 col-lg-3">
						<article class="profile-2">
							<div class="titulo-profile">
								APAFA
							</div>
							<h2 class="profile-username">2024-2025</h2>
							<h2 class="profile-username"><span class="label label-warning">PROCESO</span></h2>

						</article>
					</div>
				</div>


		</div>
</div>					
