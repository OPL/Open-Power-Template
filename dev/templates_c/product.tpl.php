

	<h1><?php echo htmlspecialchars($this->_data['product']->name);   ?></h1>
	
	<?php  if($this->_data['admin']){   ?>
		<ul class="admin">
			<li>
				<a href="<?php  echo htmlspecialchars('/comparator_admin/product_edit/'.$this->_data['product']->id);   ?>">edytuj produkt</a>
			</li>
			<li>
				<a href="<?php  echo htmlspecialchars('/comparator_admin/product_delete/'.$this->_data['product']->id);   ?>">usuń produkt</a>
			</li>
		</ul>
	<?php  }   ?>
	
	
	<div id="product">

		<?php  if($this->_data['product']->image){   ?>
			<img src="<?php  echo htmlspecialchars($this->_data['product']->image);   ?>" alt="<?php  echo htmlspecialchars($this->_data['product']->name);   ?>" />
		<?php  }   ?>

		<?php  if($this->_data['product']->content){   ?>
			<div id="product_content">
				<?php echo $this->_data['product']->content;   ?>
			</div>
		<?php  }   ?>

	</div>

	

	<?php $_sect1_vals =  $this->_data['offers']; if(is_array($_sect1_vals) && ($_sect1_cnt = sizeof($_sect1_vals)) > 0){   ?>
		
		<table class="offers">
			<thead>
				<th class="shop_icon">
					Sklep
				</th>
				<th class="product_name">
					Nazwa w sklepie
				</th>
				<th class="product_price">
					Cena
				</th>
			</thead>
		
		<?php for($_sect1_i = 0; $_sect1_i < $_sect1_cnt; $_sect1_i++){   ?>

				<tr>
					<td class="shop_icon">
					
						<?php  if($_sect1_vals[$_sect1_i]->link){   ?>
							<a href="<?php  echo htmlspecialchars($_sect1_vals[$_sect1_i]->link);   ?>">
								<img src="<?php  echo htmlspecialchars($_sect1_vals[$_sect1_i]->shop_icon);   ?>" />
							</a>
						
						<?php }else{   ?>
							<img src="<?php  echo htmlspecialchars($_sect1_vals[$_sect1_i]->shop_icon);   ?>" />
						<?php  }   ?>
	
					</td>
					<td class="product_name">
						<?php  if($this->_data['admin']){   ?>
							<a href="<?php  echo htmlspecialchars('/comparator_admin/offer_edit/'.$_sect1_vals[$_sect1_i]->shop_id.'/'.$this->_data['product']->id);   ?>" class="admin">
								<img src="/img/admin/edit.png" alt="edytuj" />
							</a>
							<a href="<?php  echo htmlspecialchars('/comparator_admin/offer_delete/'.$_sect1_vals[$_sect1_i]->shop_id.'/'.$this->_data['product']->id);   ?>" class="admin">
								<img src="/img/admin/delete.png" alt="usuń" />
							</a>
						<?php  }   ?>
						<?php echo htmlspecialchars($_sect1_vals[$_sect1_i]->name);   ?>
					</td>
					<td class="product_price">
						<?php  if($_sect1_vals[$_sect1_i]->link){   ?>
							<a href="<?php  echo htmlspecialchars($_sect1_vals[$_sect1_i]->link);   ?>">
								<?php echo htmlspecialchars($_sect1_vals[$_sect1_i]->price);   ?> zł
							</a>
						
						<?php }else{   ?>
							<?php echo htmlspecialchars($_sect1_vals[$_sect1_i]->price);   ?> zł
						<?php  }   ?>
					</td>
					<?php  if($_sect1_vals[$_sect1_i]->link){   ?>
					<td class="shop_link">
						<a href="<?php  echo htmlspecialchars($_sect1_vals[$_sect1_i]->link);   ?>">przejdź do sklepu</a>
					</td>
					<?php  }   ?>
				</tr>
		<?php  }   ?>

		<?php  if($this->_data['admin']){   ?>
			<tr>
				<td>
				</td>
				<td class="admin">
					<a href="<?php  echo htmlspecialchars('/comparator_admin/offer_add/'.$this->_data['product']->id);   ?>">dodaj ofertę</a>
				</td>
				<td>
				</td>
			</tr>		
		<?php  }   ?>

		</table>


		<?php  } else {   ?>
			<p>Tego produktu nie oferuje żaden sklep.</p>		
				
	
	<?php  }   ?>

