<?php
namespace GoCache;

if ( ! function_exists( 'add_action' ) ) {
	exit(0);
}

class Settings_View extends View
{
	public static function render_start_page()
	{
		?>

		<div id="gocache-admin" class="wrap gocache-settings">

			<div class="row">

				<div class="main-content">

					<div class="gocache-logo">
						<img src="<?php echo esc_url( App::plugins_url( 'assets/images/logo-gocache.png' )); ?>">
					</div>

					<h3><?php _e( 'Start', App::TEXTDOMAIN ); ?></h3>

					<p>
						<?php _e( 'Conecta seu WordPress com a GoCache, CDN de última geração, que acelera de forma inteligente as páginas e arquivos estáticos do site, reduzindo o consumo de recursos no servidor web e banco de dados.

						Para mais informações sobre a instalação e configuração do plugin', App::TEXTDOMAIN); ?>, <a target="_blank" href="https://gocache.zendesk.com/hc/pt-br/articles/226255027"><?php _e('clique aqui', App::TEXTDOMAIN); ?></a>.
					</p>

				</div>

			</div>

		</div>

		<?php
	}
	public static function render_authenticate_page()
	{
		$setting = new Setting();

		$authentication = new Authentication_Controller();
		$response       = $authentication->authentication();

		$connected = ( $response && $response['status'] == 1 ) ? true : false;

		self::_render_notice( $connected, $response['message'] );

		?>

		<div id="gocache-admin" class="wrap gocache-settings">

			<div class="row">

				<div class="main-content">

					<div class="gocache-logo">
						<img src="<?php echo esc_url( App::plugins_url( 'assets/images/logo-gocache.png' )); ?>">
					</div>

					<form action="options.php" method="post">
						<?php settings_fields( 'gocache-manager-settings' ); ?>
						<?php do_settings_sections( 'gocache-manager-settings' ); ?>

						<div class="form-header">
							<h3 class="title-form">
								<?php _e( 'API Settings', App::TEXTDOMAIN ); ?>
							</h3>
							<span class="help-link">
								<a target="_blank" title="Ajuda?" href="https://gocache.zendesk.com/hc/pt-br/articles/115001264228">?</a>
							</span>
						</div>

						<table class="form-table">

							<tr valign="top">
								<th scope="row">
									<?php _e( 'Status', App::TEXTDOMAIN ); ?>
								</th>
								<td>
									<?php if ( $connected ) : ?>
										<span class="status positive"><?php _e( 'CONNECTED' , App::TEXTDOMAIN ); ?></span>
									<?php else : ?>
										<span class="status neutral"><?php _e( 'NOT CONNECTED', App::TEXTDOMAIN ); ?></span>
									<?php endif; ?>
								</td>
							</tr>

							<tr valign="top">
								<th scope="row"><label for="gocache_api_key"><?php _e( 'API Key', App::TEXTDOMAIN ); ?></label></th>
								<td>
									<input type="password" class="regular-text"
										   placeholder="<?php _e( 'Your GoCache API key', App::TEXTDOMAIN ); ?>"
										   id="gocache_api_key"
										   name="<?php _e( $setting->get_option_name( 'api_key' ),App::TEXTDOMAIN ); ?>"
										   value="<?php _e( $setting->api_key, App::TEXTDOMAIN ); ?>" />
									<p class="help">
										<?php _e( 'Digite sua API Token(Chave da API) para conectar com a sua conta na GoCache.', App::TEXTDOMAIN ); ?>
										<a target="_blank" href="https://gocache.zendesk.com/hc/pt-br/articles/115001135087)">Ajuda?</a>
									</p>
								</td>
							</tr>

							<tr valign="top">
								<th scope="row"><label for="gocache_domain"><?php _e( 'Domain', App::TEXTDOMAIN ); ?></label></th>
								<td>
									<?php $domain = preg_replace( '(https?://)', '', Utils::get_domain() ); ?>
									<p class="description"><?php ! $domain ? _e( '—', App::TEXTDOMAIN) : _e( $domain, App::TEXTDOMAIN ); ?></p>

									<input type="hidden"
										   name="<?php _e( $setting->get_option_name( 'domain' ), App::TEXTDOMAIN ); ?>"
										   value="<?php _e( $domain, App::TEXTDOMAIN) ?>" />
								</td>
						</table>

						<?php submit_button(); ?>

					</form>

				</div>

			</div>

		</div>

		<?php
	}

	public static function render_general_page()
	{
		$setting = new Setting();

		$authentication = new Authentication_Controller();
		$authentication->authentication();

		$configs   = get_option( 'gocache_option-external_configs' );

		?>
		<div id="gocache-admin" class="wrap gocache-settings">

			<div class="main-content">

				<div class="gocache-logo">
					<img src="<?php echo esc_url( App::plugins_url( 'assets/images/logo-gocache.png' )); ?>">
				</div>

				<form action="<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>"
					  data-extend="gocache-ajax"
					  data-component="gocache-update">

					<div class="form-header">
						<h3 class="title-form">
							<?php _e( 'General Settings', App::TEXTDOMAIN ); ?>
						</h3>
						<span class="help-link">
							<a target="_blank" title="Ajuda?" href="https://gocache.zendesk.com/hc/pt-br/articles/115001216367">?</a>
						</span>
					</div>

					<table class="form-table">

						<tr valign="top">
							<th scope="row"><label for="gocache_smart_cache"><?php _e( 'SmartCache for WordPress', App::TEXTDOMAIN ); ?></label></th>
							<td>
								<input type="checkbox"
									   id="gocache_smart_cache"
									   name="smart_status"
									   value="true"
									   <?php checked( $configs->response->smart_status, 'true' ); ?> />

								<p class="description">​<?php _e( 'Marque para habilitar o cache de conteúdo dinâmico', App::TEXTDOMAIN ); ?></p>
							</td>
						</tr>
						<?php
							$cache_options = array(
								-1     => __( 'Disabled', App::TEXTDOMAIN ),
								604800 => __( '7 days', App::TEXTDOMAIN ),
								172800 => __( '2 days', App::TEXTDOMAIN ),
								86400  => __( '1 day', App::TEXTDOMAIN ),
								14400  => __( '4 hours', App::TEXTDOMAIN ),
								7200   => __( '2 hours', App::TEXTDOMAIN ),
								3600   => __( '1 hour', App::TEXTDOMAIN ),
								1800   => __( '30 minutes', App::TEXTDOMAIN ),
								900    => __( '15 minutes', App::TEXTDOMAIN ),
								600    => __( '10 minutes', App::TEXTDOMAIN ),
								300    => __( '5 minutes', App::TEXTDOMAIN ),
							);
						?>
						<tr valign="top">
							<th scope="row"><label for="gocache_expiration_cache"><?php _e( 'Cache expiration time', App::TEXTDOMAIN ); ?></label></th>
							<td>
								<select name="smart_ttl" id="gocache_expiration_cache">
								<?php
									foreach ( $cache_options as $time => $text ) :
										printf( '<option %s value="%d">%s</option>', selected( $configs->response->smart_ttl, $time, false ), $time, $text );
									endforeach;
								?>
								</select>
								<p class="description"><?php _e( '​​Defina o tempo de expiração de cache na CDN', App::TEXTDOMAIN ); ?></p>
							</td>
						</tr>

						<tr valign="top">
							<th scope="row"><label for="gocache_gzip"><?php _e( 'GZIP Compression', App::TEXTDOMAIN ); ?></label></th>
							<td>
								<input type="checkbox"
									   id="gocache_gzip"
									   name="gzip_status"
									   value="true"
									   <?php checked( $configs->response->gzip_status, 'true' ); ?> />
								<p class="description"><?php _e( '​​Marque para habilitar a compressão GZIP', App::TEXTDOMAIN ); ?></p>
							</td>
						</tr>
						<?php
							$browser_options = array(
								-1       => __( 'Disabled', App::TEXTDOMAIN ),
								3600     => __( '1 hour', App::TEXTDOMAIN ),
								7200     => __( '2 hours', App::TEXTDOMAIN ),
								14400    => __( '4 hours', App::TEXTDOMAIN ),
								43200    => __( '12 hours', App::TEXTDOMAIN ),
								86400    => __( '1 day', App::TEXTDOMAIN ),
								172800   => __( '2 days', App::TEXTDOMAIN ),
								345600   => __( '4 days', App::TEXTDOMAIN ),
								604800   => __( '7 days', App::TEXTDOMAIN ),
								1296000  => __( '15 days', App::TEXTDOMAIN ),
								2592000  => __( '30 days', App::TEXTDOMAIN ),
								15552000 => __( '6 months', App::TEXTDOMAIN ),
								31536000 => __( '1 year', App::TEXTDOMAIN ),
							);
						?>

						<tr valign="top">
							<th scope="row"><label for="gocache_browsercache"><?php _e( 'Browser Cache', App::TEXTDOMAIN ); ?></label></th>
							<td>
								<select name="expires_ttl" id="gocache_browsercache">
								<?php
									foreach ( $browser_options as $time => $text ) :
										printf( '<option %s value="%d">%s</option>', selected( $configs->response->expires_ttl, $time, false ), $time, $text );
									endforeach;
								?>
								</select>
								<p class="description"><?php _e( '​​​Defina o tempo de cache no navegador', App::TEXTDOMAIN ); ?></p>
							</td>
						</tr>

						<tr valign="top">
							<th scope="row"><label for="gocache_debug_mode"><?php _e( 'Development Mode', App::TEXTDOMAIN ); ?></label></th>
							<td>
								<input type="checkbox"
									   id="gocache_debug_mode"
									   name="deploy_mode"
									   value="true"
									   <?php checked( $configs->response->deploy_mode, 'true' ); ?> />
								<p class="description">​​<?php _e( 'Marque para habilitar o modo de desenvolvimento', App::TEXTDOMAIN ); ?></p>
							</td>
						</tr>

					</table>

					<p class="submit">
						<?php $setting->the_nonce_field(); ?>
						<input type="hidden" name="action" value="CBS93bVqAwWnVFHw">
						<input type="submit" class="button button-primary" value="<?php _e( 'Save settings', App::TEXTDOMAIN ); ?>" data-element="submit">
					</p>

				</form>

			</div>

		</div>
		<?php
	}

	public static function render_cache_page()
	{
		$setting = new Setting();

		$authentication = new Authentication_Controller();
		$authentication->authentication();

		?>
		<div id="gocache-admin" class="wrap gocache-settings">

			<div class="main-content">

				<div class="gocache-logo">
					<img src="<?php echo esc_url( App::plugins_url( 'assets/images/logo-gocache.png' )); ?>">
				</div>

				<form action="<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>" data-component="gocache-ajax">

					<div class="form-header">
						<h3 class="title-form">
							<?php _e( 'Cache Settings', App::TEXTDOMAIN ); ?>
						</h3>
						<span class="help-link">
							<a target="_blank" title="Ajuda?" href="https://gocache.zendesk.com/hc/pt-br/articles/115001264428">?</a>
						</span>
					</div>

					<table data-component="clear" class="form-table">

						<tr valign="top">
							<th scope="row"><label for="gocache_clear_cache"><?php _e( 'Limpar automaticamente o cache a cada novo conteúdo publicado ou alterado', App::TEXTDOMAIN ); ?></label></th>
							<td>
								<input type="radio"
									   id="gocache_clear_cache_yes"
									   name="<?php _e( $setting->get_option_name( 'auto_clear_cache' ), App::TEXTDOMAIN); ?>"
									   value="yes"
									   data-action="auto-clear-yes"
									   <?php _e( checked( $setting->auto_clear_cache, 'yes' ). App::TEXTDOMAIN); ?> />
								<label for="gocache_clear_cache_yes"><?php _e( 'Yes', App::TEXTDOMAIN ); ?>&nbsp;&nbsp;</label>

								<input type="radio"
									   id="gocache_clear_cache_no"
									   name="<?php _e( $setting->get_option_name( 'auto_clear_cache' ), App::TEXTDOMAIN); ?>"
									   value="no"
									   data-action="auto-clear-no"
									   <?php _e( checked( $setting->auto_clear_cache, 'no' ), App::TEXTDOMAIN); ?> />
								<label for="gocache_clear_cache_no"><?php _e( 'No', App::TEXTDOMAIN ); ?></label>

								<p class="help">
									<a target="_blank" href="https://gocache.zendesk.com/hc/pt-br/articles/224896487-Por-que-%C3%A9-importante-limpar-o-cache-automaticamente-"><?php _e( 'Why it is important to automatically clear the cache?', App::TEXTDOMAIN ); ?></a>
								</p>
							</td>
						</tr>
						<div id="optionsSection">
							<tr valign="top" class="optionsSection">
								<th scope="row"> <?php _e( 'Purge Sitemap: ', App::TEXTDOMAIN ) ?></label></th>
								<td>
								<?php $checked = get_option( 'gocache_option-auto_clear_sitemap_url' ); ?>
									<input  type="checkbox"
											name="sitemap_checkbox"
											id="gocache_sitemap_checkbox"
											data-action="sitemap-auto-clear"
											data-element="sitemap"
											<?php _e( $checked == 'yes' ? 'checked' : '', App::TEXTDOMAIN); ?> />
									<label for="gocache_sitemap_checkbox"><?php _e( 'Limpar automaticamente cache do sitemap quando um post for alterado.', App::TEXTDOMAIN ); ?></label>

								</td>
							</tr>

							<tr valign="top" class="optionsSection">
								<th scope="row"> <?php _e( 'Purge AMP Post: ', App::TEXTDOMAIN ) ?></label></th>
								<td>
								<?php $checked = get_option( 'gocache_option-auto_clear_amp_url' ); ?>
									<input  type="checkbox"
											name="amp_checkbox"
											id="gocache_amp_checkbox"
											data-action="amp-auto-clear"
											data-element="amp"
											<?php _e( $checked == 'yes' ? 'checked' : '', App::TEXTDOMAIN); ?> />
									<label for="gocache_amp_checkbox"><?php _e( 'Limpar automaticamente cache de URLs AMPs quando um post for alterado.', App::TEXTDOMAIN ); ?></label>

								</td>
							</tr>

						</div>

						<tr valign="top">
							<th scope="row"><label for="gocache_clear_strings"><?php _e( 'Customizar chave de cache com uma string', App::TEXTDOMAIN) ?></label></th>
							<td>
								<textarea class="large-text"
										  name="<?php _e( $setting->get_option_name( 'clear_cache_strings' ), App::TEXTDOMAIN); ?>"
										  id="gocache_clear_strings" rows="5"><?php _e( $setting->clear_cache_strings, App::TEXTDOMAIN ); ?></textarea>
								<p class="description"><?php _e( 'Insira uma string por linha (máximo de 3).', App::TEXTDOMAIN ); ?></p>
							</td>
						</tr>

						<tr valign="top">
							<th scope="row"><?php _e( 'Limpar todo cache', App::TEXTDOMAIN ); ?></th>
							<td>
								<input type="button" class="button button-secondary" data-action="all" value="<?php _e( 'Clear', App::TEXTDOMAIN ); ?>"/>
							</td>
						</tr>

						<tr valign="top">
							<th scope="row"><label for="gocache_clear_by_url"><?php _e( 'Limpar cache por URL', App::TEXTDOMAIN ); ?></label></th>
							<td>
								<textarea class="large-text" placeholder="https://www.gocache.com.br" data-element="textarea" id="gocache_clear_by_url" rows="5"></textarea>
								<p class="description"><?php _e( 'Insira uma URL por linha.', App::TEXTDOMAIN ); ?></p>
								<p>
									<input type="button" class="button button-secondary" data-action="by-url" value="<?php _e( 'Clear', App::TEXTDOMAIN ); ?>"/>
								</p>
							</td>
						</tr>

						<tr valign="top">
							<th scope="row"><label for="gocache_clear_urls"><?php _e( 'Salvar URLs a serem limpas', App::TEXTDOMAIN ); ?></label></th>
							<td>
							<textarea class="large-text"
										  name="<?php _e( $setting->get_option_name( 'clear_cache_urls' ), App::TEXTDOMAIN); ?>"
										  id="gocache_clear_urls" rows="5"><?php _e( $setting->clear_cache_urls, App::TEXTDOMAIN ); ?></textarea>
								<p class="description"><?php _e( 'Salve aqui as URLs que deseja que sejam limpadas sempre que o cache do site for purgado.', App::TEXTDOMAIN ); ?></p>
								<p class="description"><?php _e( 'Insira uma URL por linha.', App::TEXTDOMAIN ); ?></p>
							</td>
						</tr>

						<tr valign="top">
							<th scope="row"><label for="gocache_override_url_domain"><?php _e( 'Sobrescrever URL do domínio', App::TEXTDOMAIN ); ?></label></th>
							<td>
								<textarea class="large-text"
										  name="<?php _e( $setting->get_option_name( 'override_url_domain' ), App::TEXTDOMAIN); ?>"
										  placeholder="www.gocache.com.br"
										  id="gocache_override_url_domain" rows="5"><?php _e($setting->override_url_domain, App::TEXTDOMAIN); ?></textarea>
								<p class="description"><?php _e( 'Insira um domínio/subdomínio por linha.', App::TEXTDOMAIN );  ?></p>
							</td>
						</tr>

					</table>

					<p class="submit">
						<?php _e( $setting->the_nonce_field(), App::TEXTDOMAIN); ?>
						<input type="hidden" name="action" value="settings_gocache_save">
						<input type="submit" class="button button-primary" value="<?php _e( 'Save settings', App::TEXTDOMAIN ); ?>" data-element="submit">
					</p>

					<script type="text/javascript">
						jQuery('document').ready(function(){
							jQuery('#gocache_clear_strings').on( 'keyup', function(e){
								if ( this.value ) {
									var pieces = this.value.split('\n');
									pieces = pieces.slice(0,3);
									pieces = pieces.map(function(text){
										return text.substring(0, 20);
									});
									this.value = pieces.join('\n');
								}
							});
						});
					</script>

				</form>

			</div>

		</div>
		<?php
	}

	private static function _render_notice( $connected, $message )
	{
		if ( ! $message  || ! isset( $_REQUEST['settings-updated'] ) ) {
			return;
		}

		$notice_type = 'notice-error';

		if ( $connected ) {
			$notice_type    = 'notice-success';
			$message = 'Conexão realizada com sucesso.';
		}

		?>
		<div id="message" class="notice <?php _e( $notice_type, App::TEXTDOMAIN ); ?> is-dismissible">
			<p><?php _e( ucfirst( $message ), App::TEXTDOMAIN ); ?></p>
		</div>
		<?php
	}
}
