<?php

/**
 * The user interface and activation/deactivation methods for administering
 * the Login Security Solution WordPress plugin
 *
 * @package login-security-solution
 * @link https://wordpress.org/plugins/login-security-solution/
 * @license http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 * @author Daniel Convissor <danielc@analysisandsolutions.com>
 * @copyright The Analysis and Solutions Company, 2012-2015
 */

/**
 * The user interface and activation/deactivation methods for administering
 * the Login Security Solution WordPress plugin
 *
 * @package login-security-solution
 * @link https://wordpress.org/plugins/login-security-solution/
 * @license http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 * @author Daniel Convissor <danielc@analysisandsolutions.com>
 * @copyright The Analysis and Solutions Company, 2012-2015
 */
class login_security_solution_admin extends login_security_solution {
	/**
	 * The WP privilege level required to use the admin interface
	 * @var string
	 */
	protected $capability_required;

	/**
	 * Metadata and labels for each element of the plugin's options
	 * @var array
	 */
	protected $fields;

	/**
	 * URI for the forms' action attributes
	 * @var string
	 */
	protected $form_action;

	/**
	 * Name of the page holding the options
	 * @var string
	 */
	protected $page_options;

	/**
	 * Title for the plugin's settings page
	 * @var string
	 */
	protected $text_settings;


	/**#@+
	 * NON-STANDARD: These properties are for the password change page.
	 */
	/**
	 * Key for the change password "don't remind me" checkbox
	 * @var string
	 */
	protected $key_checkbox_remind = 'check_remind';

	/**
	 * Key for the change password "confirmation" checkbox
	 * @var string
	 */
	protected $key_checkbox_require = 'check_reqire';

	/**
	 * ID slug for the plugin's password change page
	 * @var string
	 */
	protected $option_pw_force_change_name;

	/**
	 * Text for the plugin's password change page "don't remind me" button
	 * @var string
	 */
	protected $text_button_remind;

	/**
	 * Text for the plugin's password change page require button
	 * @var string
	 */
	protected $text_button_require;

	/**
	 * Title for the plugin's password change page
	 * @var string
	 */
	protected $text_pw_force_change;
	/**#@-*/


	/**
	 * Sets the object's properties and options
	 *
	 * @return void
	 *
	 * @uses login_security_solution::initialize()  to set the object's
	 *	      properties
	 * @uses login_security_solution_admin::set_fields()  to populate the
	 *       $fields property
	 */
	public function __construct() {
		$this->initialize();
		$this->set_fields();

		// Translation already in WP combined with plugin's name.
		$this->text_settings = self::NAME . ' ' . __('Settings');

		if (is_multisite()) {
			$this->capability_required = 'manage_network_options';
			$this->form_action = '../options.php';
			$this->page_options = 'settings.php';
		} else {
			$this->capability_required = 'manage_options';
			$this->form_action = 'options.php';
			$this->page_options = 'options-general.php';
		}

		// NON-STANDARD: This is for the password change page.
		$this->option_pw_force_change_name = self::ID . '-pw-force-change-done';
		$this->text_pw_force_change = __("Change All Passwords", 'login-security-solution');
		$this->text_button_remind = __("Do not remind me about this", 'login-security-solution');
		$this->text_button_require = __("Require All Passwords Be Changed", 'login-security-solution');
	}

	/*
	 * ===== ACTIVATION & DEACTIVATION CALLBACK METHODS =====
	 */

	/**
	 * Establishes the tables and settings when the plugin is activated
	 * @return void
	 */
	public function activate() {
		global $wpdb;

		if (is_multisite() && !is_network_admin()) {
			die($this->hsc_utf8(sprintf(__("%s must be activated via the Network Admin interface when WordPress is in multistie network mode.", 'login-security-solution'), self::NAME)));
		}

		/*
		 * Create or alter the plugin's tables as needed.
		 */

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		// Note: dbDelta() requires two spaces after "PRIMARY KEY".  Werid.
		// WP's insert/prepare/etc don't handle NULL's (at least in 3.3).
		// It also requires the keys to be named and there to be no space
		// the column name and the key length.
		$sql = "CREATE TABLE `$this->table_fail` (
				fail_id BIGINT(20) NOT NULL AUTO_INCREMENT,
				ip VARCHAR(39) NOT NULL DEFAULT '',
				user_login VARCHAR(60) NOT NULL DEFAULT '',
				pass_md5 varchar(64) NOT NULL DEFAULT '',
				date_failed TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
				PRIMARY KEY  (fail_id),
				KEY ip (ip(9)),
				KEY user_login (user_login(5)),
				KEY pass_md5 (pass_md5(10))
				)";

		dbDelta($sql);
		if ($wpdb->last_error) {
			die($wpdb->last_error);
		}

		/*
		 * Save this plugin's options to the database.
		 */

		if (is_multisite()) {
			switch_to_blog(1);
		}
		update_option($this->option_name, $this->options);
		add_option($this->option_pw_force_change_name, 0, '', 'no');
		if (is_multisite()) {
			restore_current_blog();
		}

		/*
		 * Store password hashes.
		 */

		if ($this->options['pw_reuse_count']) {
			$i = 0;

			while (true) {
				$sql = "SELECT ID, user_pass FROM `$wpdb->users`
					LIMIT $i, 1000";

				$result = $wpdb->get_results($sql, ARRAY_A);
				if (!$result) {
					if ($i == 0) {
						die(self::ID . ' could not find users.');
					} else {
						break;
					}
				}

				foreach ($result as $user) {
					if (!$this->save_pw_hash($user['ID'], $user['user_pass'])) {
						die(self::ID . ' could not save password hash.');
					}
				}

				$i += 1000;
			}
		}
	}

	/**
	 * Removes the tables and settings when the plugin is deactivated
	 * if the deactivate_deletes_data option is turned on
	 * @return void
	 */
	public function deactivate() {
		global $wpdb;

		$prior_error_setting = $wpdb->show_errors;
		$wpdb->show_errors = false;
		$denied = 'command denied to user';

		$wpdb->query("DROP TABLE `$this->table_fail`");
		if ($wpdb->last_error) {
			if (strpos($wpdb->last_error, $denied) === false) {
				die($wpdb->last_error);
			}
		}

		$wpdb->show_errors = $prior_error_setting;

		$package_id = self::ID;
		$wpdb->escape_by_ref($package_id);

		$wpdb->query("DELETE FROM `$wpdb->options`
				WHERE option_name LIKE '$package_id%'");

		$wpdb->query("DELETE FROM `$wpdb->usermeta`
				WHERE meta_key LIKE '$package_id%'");
	}

	/*
	 * ===== ADMIN USER INTERFACE =====
	 */

	/**
	 * Sets the metadata and labels for each element of the plugin's
	 * options
	 *
	 * @return void
	 * @uses login_security_solution_admin::$fields  to hold the data
	 */
	protected function set_fields() {
		$this->fields = array(
			'idle_timeout' => array(
				'group' => 'misc',
				'label' => __("Idle Timeout", 'login-security-solution'),
				'text' => __("Close inactive sessions after this many minutes. 0 disables this feature.", 'login-security-solution'),
				'type' => 'int',
			),
			'block_author_query' => array(
				'group' => 'misc',
				'label' => __("Block Author Query", 'login-security-solution'),
				'text' => __("Prevent attackers from discovering your users' names via the '?author=<id>' URI.", 'login-security-solution'),
				'type' => 'bool',
				'bool0' => __("Off, allow discovering authors.", 'login-security-solution'),
				'bool1' => __("On, redirect requests to the home page.", 'login-security-solution'),
			),
			'disable_logins' => array(
				'group' => 'misc',
				'label' => __("Maintenance Mode", 'login-security-solution'),
				'text' => __("Disable logins from users who are not administrators and disable posting of comments?", 'login-security-solution'),
				'type' => 'bool',
				'bool0' => __("Off, let all users log in.", 'login-security-solution'),
				'bool1' => __("On, disable comments and only let administrators log in.", 'login-security-solution'),
			),
			'deactivate_deletes_data' => array(
				'group' => 'misc',
				'label' => __("Deactivation", 'login-security-solution'),
				'text' => __("Should deactivating the plugin remove all of the plugin's data and settings?", 'login-security-solution'),
				'type' => 'bool',
				'bool0' => __("No, preserve the data for future use.", 'login-security-solution'),
				'bool1' => __("Yes, delete the damn data.", 'login-security-solution'),
			),

			'login_fail_minutes' => array(
				'group' => 'login',
				'label' => __("Match Time", 'login-security-solution'),
				'text' => __("How far back, in minutes, should login failures look for matching data? 0 disables Login Failure slow downs, notifications and breach confirmations.", 'login-security-solution'),
				'type' => 'int',
			),
			'login_fail_tier_2' => array(
				'group' => 'login',
				'label' => __("Delay Tier 2", 'login-security-solution'),
				'text' => sprintf(__("How many matching login failures should it take to get into this (%d - %d second) Delay Tier? Must be >= %d.", 'login-security-solution'), 4, 30, 2),
				'type' => 'int',
				'greater_than' => 2,
			),
			'login_fail_tier_3' => array(
				'group' => 'login',
				'label' => __("Delay Tier 3", 'login-security-solution'),
				'text' => sprintf(__("How many matching login failures should it take to get into this (%d - %d second) Delay Tier? Must be > Delay Tier 2.", 'login-security-solution'), 25, 60),
				'type' => 'int',
			),
			'login_fail_tier_dos' => array(
				'group' => 'login',
				'label' => __("DoS Tier", 'login-security-solution'),
				'text' => __("How many matching login failures should it take until the plugin stops delaying responses (to avoid a Denial of Service problem)? 0 disables this feature. Must be > Delay Tier 3.", 'login-security-solution'),
				'type' => 'int',
			),
			'admin_email' => array(
				'group' => 'login',
				'label' => __("Notifications To", 'login-security-solution'),
				'text' => __("The email address(es) the failure and breach notifications should be sent to. For multiple addresses, separate them with commas. WordPress' 'admin_email' setting is used if none is provided here.", 'login-security-solution'),
				'type' => 'string',
			),
			'login_fail_notify' => array(
				'group' => 'login',
				'label' => __("Failure Notification", 'login-security-solution'),
				'text' => __("Notify the administrator after x matching login failures. 0 disables this feature.", 'login-security-solution'),
				'type' => 'int',
			),
			'login_fail_notify_multiple' => array(
				'group' => 'login',
				'label' => __("Multiple Failure Notifications", 'login-security-solution'),
				'text' => __("Should multiple failure notifications be sent to the administrators?", 'login-security-solution'),
				'type' => 'bool',
				'bool0' => __("No, just notify them the first time that x matching login failures happen.", 'login-security-solution'),
				'bool1' => __("Yes, notify them upon every x matching login failures.", 'login-security-solution'),
			),
			'login_fail_breach_notify' => array(
				'group' => 'login',
				'label' => __("Breach Notification", 'login-security-solution'),
				'text' => __("Notify the administrator if a successful login uses data matching x login failures. 0 disables this feature.", 'login-security-solution'),
				'type' => 'int',
			),
			'login_fail_breach_pw_force_change' => array(
				'group' => 'login',
				'label' => __("Breach Email Confirm", 'login-security-solution'),
				'text' => __("If a successful login uses data matching x login failures, immediately log the user out and require them to use WordPress' lost password process. 0 disables this feature.", 'login-security-solution'),
				'type' => 'int',
			),

			'pw_length' => array(
				'group' => 'pw',
				'label' => __("Length", 'login-security-solution'),
				'text' => sprintf(__("How long must passwords be? Must be >= %d.", 'login-security-solution'), 10),
				'type' => 'int',
				'greater_than' => 10,
			),
			'pw_complexity_exemption_length' => array(
				'group' => 'pw',
				'label' => __("Complexity Exemption", 'login-security-solution'),
				'text' => sprintf(__("How long must passwords be to be exempt from the complexity requirements? Must be >= %d.", 'login-security-solution'), 20),
				'type' => 'int',
				'greater_than' => 20,
			),
			'pw_dict_file' => array(
				'group' => 'pw',
				'label' => __("Dict Program Index File", 'login-security-solution'),
				'text' => sprintf(__("The path and name of the 'dict' index file to examine for common words.", 'login-security-solution')),
				'type' => 'string',
			),
			'pw_change_days' => array(
				'group' => 'pw',
				'label' => __("Aging", 'login-security-solution'),
				'text' => __("How many days old can a password be before requiring it be changed? Not recommended. 0 disables this feature.", 'login-security-solution'),
				'type' => 'int',
			),
			'pw_change_grace_period_minutes' => array(
				'group' => 'pw',
				'label' => __("Grace Period", 'login-security-solution'),
				'text' => sprintf(__("How many minutes should a user have to change their password once they know it has expired? Must be >= %d.", 'login-security-solution'), 5),
				'type' => 'int',
				'greater_than' => 5,
			),
			'pw_reuse_count' => array(
				'group' => 'pw',
				'label' => __("History", 'login-security-solution'),
				'text' => __("How many passwords should be remembered? Prevents reuse of old passwords. 0 disables this feature.", 'login-security-solution'),
				'type' => 'int',
			),

			'login_fail_delete_interval' => array(
				'group' => 'retention',
				'label' => __("Deletion Interval", 'login-security-solution'),
				'text' => sprintf(__("Run the deletion process upon every x login failures. 0 disables this feature. Suggested value: %d.", 'login-security-solution'), 1000),
				'type' => 'int',
			),
			'login_fail_delete_days' => array(
				'group' => 'retention',
				'label' => __("Deletion Days", 'login-security-solution'),
				'text' => __("Delete records older than x days.", 'login-security-solution'),
				'type' => 'int',
			),
		);
	}

	/**
	 * A filter to add a "Settings" link in this plugin's description
	 *
	 * NOTE: This method is automatically called by WordPress for each
	 * plugin being displayed on WordPress' Plugins admin page.
	 *
	 * @param array $links  the links generated thus far
	 * @return array
	 */
	public function plugin_action_links($links) {
		// Translation already in WP.
		$links[] = '<a href="' . $this->hsc_utf8($this->page_options)
				. '?page=' . self::ID . '">'
				. $this->hsc_utf8(__('Settings')) . '</a>';

		// NON-STANDARD: This is for the password change page.
		$links[] = '<a href="' . $this->hsc_utf8($this->page_options)
				. '?page=' . $this->hsc_utf8($this->option_pw_force_change_name)
				. '">' . $this->hsc_utf8($this->text_pw_force_change) . '</a>';

		return $links;
	}

	/**
	 * Declares a menu item and callback for this plugin's settings page
	 *
	 * NOTE: This method is automatically called by WordPress when
	 * any admin page is rendered
	 */
	public function admin_menu() {
		add_submenu_page(
			$this->page_options,
			$this->text_settings,
			self::NAME,
			$this->capability_required,
			self::ID,
			array(&$this, 'page_settings')
		);
	}

	/**
	 * Declares the callbacks for rendering and validating this plugin's
	 * settings sections and fields
	 *
	 * NOTE: This method is automatically called by WordPress when
	 * any admin page is rendered
	 */
	public function admin_init() {
		register_setting(
			$this->option_name,
			$this->option_name,
			array(&$this, 'validate')
		);

		add_settings_section(
			self::ID . '-login',
			$this->hsc_utf8(__("Login Failure Policies", 'login-security-solution')),
			array(&$this, 'section_login'),
			self::ID
		);
		add_settings_section(
			self::ID . '-retention',
			$this->hsc_utf8(__("Data Retention Policies", 'login-security-solution')),
			array(&$this, 'section_retention'),
			self::ID
		);
		add_settings_section(
			self::ID . '-pw',
			$this->hsc_utf8(__("Password Policies", 'login-security-solution')),
			'__return_empty_string',
			self::ID
		);
		add_settings_section(
			self::ID . '-misc',
			$this->hsc_utf8(__("Miscellaneous Policies", 'login-security-solution')),
			'__return_empty_string',
			self::ID
		);

		// Dynamically declare each field using the info in $fields.
		foreach ($this->fields as $id => $field) {
			add_settings_field(
				$id,
				'<label for="' . $this->hsc_utf8($id) . '">'
					. $this->hsc_utf8($field['label']) . '</label>',
				array(&$this, $id),
				self::ID,
				self::ID . '-' . $field['group']
			);
		}
	}

	/**
	 * The callback for rendering the settings page
	 * @return void
	 */
	public function page_settings() {
		if (is_multisite()) {
			// WordPress doesn't show the successs/error messages on
			// the Network Admin screen, at least in version 3.3.1,
			// so force it to happen for now.
			include_once ABSPATH . 'wp-admin/options-head.php';
		}

		screen_icon('options-general');
		echo '<h2>' . $this->hsc_utf8($this->text_settings) . '</h2>';
		echo '<form action="' . $this->hsc_utf8($this->form_action) . '" method="post">' . "\n";
		settings_fields($this->option_name);
		do_settings_sections(self::ID);
		submit_button();
		echo '</form>';
	}

	/**
	 * The callback for rendering the "Login Failures Policy" section
	 * @return void
	 */
	public function section_login() {
		echo '<p>';
		echo $this->hsc_utf8(__("This plugin stores the IP address, username and password for each failed log in attempt.", 'login-security-solution'));
		echo ' ';
		echo $this->hsc_utf8(__("The data from future login failures are compared against the historical data.", 'login-security-solution'));
		echo ' ';
		echo $this->hsc_utf8(__("If any of the data points match, the plugin delays printing out the failure message.", 'login-security-solution'));
		echo ' ';
		echo $this->hsc_utf8(__("The goal is for the responses to take so long that the attackers give up and go find an easier target.", 'login-security-solution'));
		echo ' ';
		echo $this->hsc_utf8(__("The length of the delay is broken up into three tiers.", 'login-security-solution'));
		echo ' ';
		echo $this->hsc_utf8(__("The amount of the delay increases in higher tiers.", 'login-security-solution'));
		echo ' ';
		echo $this->hsc_utf8(__("The delay time within each tier is randomized to complicate profiling by attackers.", 'login-security-solution'));
		echo '</p>';
	}

	/**
	 * The callback for rendering the "Data Retention Policies" section
	 * @return void
	 */
	public function section_retention() {
		echo '<p>';
		echo $this->hsc_utf8(sprintf(__("The means for automatically deleting old records from the %s table.", 'login-security-solution'),$this->table_fail));
		echo '</p>';
	}

	/**
	 * The callback for rendering the fields
	 * @return void
	 *
	 * @uses login_security_solution_admin::input_radio()  for rendering
	 *       radio buttons
	 * @uses login_security_solution_admin::input_int()  for rendering
	 *       text input boxes
	 */
	public function __call($name, $params) {
		if (empty($this->fields[$name]['type'])) {
			return;
		}
		switch ($this->fields[$name]['type']) {
			case 'bool':
				$this->input_radio($name);
				break;
			case 'int':
				$this->input_int($name);
				break;
			case 'string':
				$this->input_string($name);
				break;
		}
	}

	/**
	 * Renders the radio button inputs
	 * @return void
	 */
	protected function input_radio($name) {
		echo $this->hsc_utf8($this->fields[$name]['text']) . '<br/>';
		echo '<input type="radio" value="0" name="'
			. $this->hsc_utf8($this->option_name)
			. '[' . $this->hsc_utf8($name) . ']"'
			. ($this->options[$name] ? '' : ' checked="checked"') . ' /> ';
		echo $this->hsc_utf8($this->fields[$name]['bool0']);
		echo '<br/>';
		echo '<input type="radio" value="1" name="'
			. $this->hsc_utf8($this->option_name)
			. '[' . $this->hsc_utf8($name) . ']"'
			. ($this->options[$name] ? ' checked="checked"' : '') . ' /> ';
		echo $this->hsc_utf8($this->fields[$name]['bool1']);
	}

	/**
	 * Renders the text input boxes for editing integers
	 * @return void
	 */
	protected function input_int($name) {
		echo '<input type="text" size="3" name="'
			. $this->hsc_utf8($this->option_name)
			. '[' . $this->hsc_utf8($name) . ']"'
			. ' id="' . $this->hsc_utf8($name) . '"'
			. ' value="' . $this->hsc_utf8($this->options[$name]) . '" /> ';
		echo $this->hsc_utf8($this->fields[$name]['text']
				. ' ' . __('Default:', 'login-security-solution') . ' '
				. $this->options_default[$name] . '.');
	}

	/**
	 * Renders the text input boxes for editing strings
	 * @return void
	 */
	protected function input_string($name) {
		echo '<input type="text" size="75" name="'
			. $this->hsc_utf8($this->option_name)
			. '[' . $this->hsc_utf8($name) . ']"'
			. ' id="' . $this->hsc_utf8($name) . '"'
			. ' value="' . $this->hsc_utf8($this->options[$name]) . '" /> ';
		echo '<br />';
		echo $this->hsc_utf8($this->fields[$name]['text']
				. ' ' . __('Default:', 'login-security-solution') . ' '
				. $this->options_default[$name] . '.');
	}

	/**
	 * Validates the user input
	 *
	 * NOTE: WordPress saves the data even if this method says there are
	 * errors.  So this method sets any inappropriate data to the default
	 * values.
	 *
	 * @param array $in  the input submitted by the form
	 * @return array  the sanitized data to be saved
	 */
	public function validate($in) {
		$out = $this->options_default;
		if (!is_array($in)) {
			// Not translating this since only hackers will see it.
			add_settings_error($this->option_name,
					$this->hsc_utf8($this->option_name),
					'Input must be an array.');
			return $out;
		}

		$gt_format = __("must be >= '%s',", 'login-security-solution');
		$default = __("so we used the default value instead.", 'login-security-solution');

		// Dynamically validate each field using the info in $fields.
		foreach ($this->fields as $name => $field) {
			if (!array_key_exists($name, $in)) {
				continue;
			}

			if (!is_scalar($in[$name])) {
				// Not translating this since only hackers will see it.
				add_settings_error($this->option_name,
						$this->hsc_utf8($name),
						$this->hsc_utf8("'" . $field['label'])
								. "' was not a scalar, $default");
				continue;
			}

			switch ($field['type']) {
				case 'bool':
					if ($in[$name] != 0 && $in[$name] != 1) {
						// Not translating this since only hackers will see it.
						add_settings_error($this->option_name,
								$this->hsc_utf8($name),
								$this->hsc_utf8("'" . $field['label']
										. "' must be '0' or '1', $default"));
						continue 2;
					}
					break;
				case 'int':
					if (!ctype_digit($in[$name])) {
						add_settings_error($this->option_name,
								$this->hsc_utf8($name),
								$this->hsc_utf8("'" . $field['label'] . "' "
										. __("must be an integer,", 'login-security-solution')
										. ' ' . $default));
						continue 2;
					}
					if (array_key_exists('greater_than', $field)
						&& $in[$name] < $field['greater_than'])
					{
						add_settings_error($this->option_name,
								$this->hsc_utf8($name),
								$this->hsc_utf8("'" . $field['label'] . "' "
										. sprintf($gt_format, $field['greater_than'])
										. ' ' . $default));
						continue 2;
					}
					break;
			}

			if ($name == 'disable_logins' && $in[$name] == 1) {
				$out[$name] = wp_get_current_user()->display_name;
			} else {
				$out[$name] = $in[$name];
			}
		}

		// Special check to make sure Delay Tier 3 > Delay Tier 2.
		$name = 'login_fail_tier_3';
		if ($out[$name] <= $out['login_fail_tier_2']) {
			add_settings_error($this->option_name,
					$this->hsc_utf8($name),
					$this->hsc_utf8("'" . $this->fields[$name]['label'] . "' "
							. sprintf($gt_format, $this->fields['login_fail_tier_2']['label'])
							. ' ' . $default));

			$out[$name] = $out['login_fail_tier_2'] + 5;
		}

		// Special check to make sure Delay Tier 4 > Delay Tier 3.
		$name = 'login_fail_tier_dos';
		if ($out[$name] && $out[$name] <= $out['login_fail_tier_3']) {
			add_settings_error($this->option_name,
					$this->hsc_utf8($name),
					$this->hsc_utf8("'" . $this->fields[$name]['label'] . "' "
							. sprintf($gt_format, $this->fields['login_fail_tier_3']['label'])
							. ' ' . $default));

			$out[$name] = $out['login_fail_tier_3'] + 5;
		}

		// Speical check to ensure reuse count is set if aging is enabled.
		$name = 'pw_reuse_count';
		if ($out['pw_change_days'] && !$out[$name]) {
			add_settings_error($this->option_name,
					$this->hsc_utf8($name),
					$this->hsc_utf8("'" . $this->fields[$name]['label'] . "' "
							. sprintf($gt_format, 1)
							. ' ' . $default));

			$out[$name] = 5;
		}

		return $out;
	}

	/*
	 * ===== NON-STANDARD: ADMIN UI FOR FORCING PASSWORD CHANGES =====
	 */

	/**
	 * Declares a menu item and callback for the force password change page
	 *
	 * NOTE: This method is automatically called by WordPress when
	 * any admin page is rendered
	 */
	public function admin_menu_pw_force_change() {
		add_submenu_page(
			$this->page_options,
			$this->text_pw_force_change,
			$this->text_pw_force_change,
			$this->capability_required,
			$this->option_pw_force_change_name,
			array(&$this, 'page_pw_force_change')
		);
	}

	/**
	 * Tells WP not to display this item in the settings menu
	 *
	 * NOTE: This method is automatically called by WordPress when
	 * any admin page is rendered
	 */
	public function admin_menu_pw_force_change_hide() {
		remove_submenu_page($this->page_options, $this->option_pw_force_change_name);
	}

	/**
	 * Declares the callbacks for rendering and validating the
	 * force password change page
	 *
	 * NOTE: This method is automatically called by WordPress when
	 * any admin page is rendered
	 */
	public function admin_init_pw_force_change() {
		register_setting(
			$this->option_pw_force_change_name,
			$this->option_pw_force_change_name,
			array(&$this, 'validate_pw_force_change')
		);

		add_settings_field(
			'checkbox',
			'',
			array(&$this, 'field_blank'),
			$this->option_pw_force_change_name
		);
		add_settings_field(
			'submit',
			'',
			array(&$this, 'field_blank'),
			$this->option_pw_force_change_name
		);
	}

	/**
	 * The callback for rendering the force password change page
	 * @return void
	 */
	public function page_pw_force_change() {
		echo '<h2>' . $this->hsc_utf8($this->text_pw_force_change) . '</h2>';

		echo '<p>';
		echo $this->hsc_utf8(__("There may be cases where everyone's password should be reset.", 'login-security-solution'));
		echo ' ';
		echo $this->hsc_utf8(sprintf(__("This page, provided by the %s plugin, offers that functionality.", 'login-security-solution'), self::NAME));
		echo '</p>';

		echo '<p>';
		echo $this->hsc_utf8(__("Submitting this form sets a flag that forces all users, except yourself, to utilize WordPress' built in password reset functionality.", 'login-security-solution'));
		echo ' ';
		echo $this->hsc_utf8(__("Users who are presently logged in will be logged out the next time they view a page that requires authentication.", 'login-security-solution'));
		echo '</p>';

		echo '<form action="' . $this->hsc_utf8($this->form_action) . '" method="post">' . "\n";
		settings_fields($this->option_pw_force_change_name);

		$this->echo_div();

		echo '<p><strong><input type="checkbox" value="1" name="'
			. $this->hsc_utf8($this->option_pw_force_change_name)
			. '[' . $this->hsc_utf8($this->key_checkbox_require)
			. ']" /> ';
		echo $this->hsc_utf8(__("Confirm that you want to force all users to change their passwords by checking this box, then click the button, below.", 'login-security-solution'));
		echo '</strong></p>';

		// This function escapes output.
		submit_button(
			$this->text_button_require,
			'primary',
			$this->option_pw_force_change_name . '[submit]'
		);

		echo "</div>\n";

		if (!$this->was_pw_force_change_done()) {
			$this->echo_div();

			echo '<p><input type="checkbox" value="1" name="'
				. $this->hsc_utf8($this->option_pw_force_change_name)
				. '[' . $this->hsc_utf8($this->key_checkbox_remind)
				. ']" /> ';
			echo $this->hsc_utf8(__("No thanks. I know what I'm doing. Please don't remind me about this.", 'login-security-solution'));
			echo '</p>';

			// This function escapes output.
			submit_button(
				$this->text_button_remind,
				'secondary',
				$this->option_pw_force_change_name . '[submit]'
			);

			echo "</div>\n";
		}

		echo '</form>';
	}

	/**
	 * Receives the user input and calls the force_change() method
	 *
	 * @param array $in  the input submitted by the form
	 * @return array  an empty array
	 *
	 * @uses login_security_solution_admin::force_change_for_all()  to flag
	 *       everyone's account with the password change requirement
	 */
	public function validate_pw_force_change($in) {
		$out = $this->was_pw_force_change_done();

		if (is_array($in)
			&& !empty($in['submit'])
			&& is_scalar($in['submit']))
		{
			$crossed = $this->hsc_utf8(__("You have checked a box that does not correspond with the button you pressed. Please check and press buttons inside the same section.", 'login-security-solution'));

			$confirm = __("Please confirm that you really want to do this. Put a check in the '%s' box before hitting the submit button.", 'login-security-solution');

			switch ($in['submit']) {
				case $this->text_button_remind:
					if (!empty($in[$this->key_checkbox_require])) {
						add_settings_error($this->option_pw_force_change_name,
								$this->hsc_utf8($this->option_pw_force_change_name),
								$crossed);
					} elseif (empty($in[$this->key_checkbox_remind])) {
						add_settings_error($this->option_pw_force_change_name,
								$this->hsc_utf8($this->option_pw_force_change_name),
								$this->hsc_utf8(sprintf($confirm, __("No thanks", 'login-security-solution'))));
					} else {
						// Translation already in WP.
						add_settings_error($this->option_pw_force_change_name,
								$this->hsc_utf8($this->option_pw_force_change_name),
								$this->hsc_utf8(__("Success!")),
								'updated');
						$out = 1;
					}
					break;
				case $this->text_button_require:
					if (!empty($in[$this->key_checkbox_remind])) {
						add_settings_error($this->option_pw_force_change_name,
								$this->hsc_utf8($this->option_pw_force_change_name),
								$crossed);
					} elseif (empty($in[$this->key_checkbox_require])) {
						add_settings_error($this->option_pw_force_change_name,
								$this->hsc_utf8($this->option_pw_force_change_name),
								$this->hsc_utf8(sprintf($confirm, __("Confirm", 'login-security-solution'))));
					} else {
						$result = $this->force_change_for_all();
						if ($result === true) {
							// Translation already in WP.
							add_settings_error($this->option_pw_force_change_name,
									$this->hsc_utf8($this->option_pw_force_change_name),
									$this->hsc_utf8(__("Success!")), 'updated');
							$out = 1;
						} else {
							add_settings_error($this->option_pw_force_change_name,
									$this->hsc_utf8($this->option_pw_force_change_name),
									$this->hsc_utf8($result));
						}
					}
					break;
			}
		}

		return $out;
	}

	/**
	 * Produces a notice at the top of each admin page, telling admins
	 * that the system is in maintenance mode
	 *
	 * NOTE: This method is automatically called by WordPress when
	 * any admin page is rendered AND our Maintenance Mode feature is on.
	 *
	 * @return void
	 */
	public function admin_notices_disable_logins() {
		if (!current_user_can('manage_options')) {
			return;
		}

		echo '<div class="error">';

		$self = false;
		if (ctype_digit($this->options['disable_logins'])) {
			// The setting's value in older versions was 0 or 1.
			$user = __('an administrator', 'login-security-solution');
		} else {
			// Version 0.42.0 began putting user's name into the setting.
			$user = $this->options['disable_logins'];
			if ($user == wp_get_current_user()->display_name) {
				$self = true;
			}
		}

		echo '<p><strong>';
		if ($self) {
			echo $this->hsc_utf8(__("NOTICE: you turned on Maintenance Mode at some point. It disables new comments and keeps non-administrators from logging in.", 'login-security-solution'));
		} else {
			echo $this->hsc_utf8(sprintf(__("WARNING: the site in Maintenance Mode. Talk to %s before making any changes.", 'login-security-solution'), $user));
		}
		echo '</strong></p>';

		if (current_user_can('administrator')) {
			echo '<p><strong>';
			echo $this->hsc_utf8(__("To turn this off, go to Settings | Login Security Solution | Maintenance Mode.", 'login-security-solution'));
			echo '</strong></p>';
		}

		echo "</div>\n";
	}

	/**
	 * Produces a notice at the top of each admin page, telling admins to
	 * run the Change All Passwords process
	 *
	 * NOTE: This method is automatically called by WordPress when
	 * any admin page is rendered AND our Change All Passwords function
	 * has not been called.
	 *
	 * @return void
	 */
	public function admin_notices_pw_force_change() {
		if (!current_user_can($this->capability_required)) {
			return;
		}

		echo '<div class="error">';

		echo '<p><strong>';
		echo $this->hsc_utf8(__("You have not asked your users to change their passwords since the plugin was activated. Most users have weak passwords. This plugin's password policies protect your site from brute force attacks. Please improve security for everyone on the Internet by making all users pick new, strong, passwords.", 'login-security-solution'));
		echo '</strong></p>';

		echo '<p><strong>';
		echo $this->hsc_utf8(__("Speaking of which, do YOU have a strong password? Make sure by changing yours too.", 'login-security-solution'));
		echo '</strong></p>';

		echo '<p><strong>';
		echo $this->hsc_utf8(__("The following link leads to a user interface where you can either require all passwords to be reset or disable this notice.", 'login-security-solution'));
		echo '</strong></p>';

		echo '<p><strong>';
		echo '<a href="' . $this->hsc_utf8($this->page_options)
			. '?page=' . $this->hsc_utf8($this->option_pw_force_change_name)
			. '">' . $this->hsc_utf8($this->text_pw_force_change) . '</a>';
		echo '</strong></p>';

		echo "\n</div>\n";
	}

	/**
	 * Produces a div tag for making borders
	 * @return void
	 */
	protected function echo_div() {
		echo '<div style="margin: 0 1em 1em 0; border: thin solid black; padding: 0 1em;">';
	}

	/**
	 * Sets a user metadata key for each user in the database, requiring
	 * them to reset their passwords
	 * @return mixed  true on success, string with error message on problem
	 */
	protected function force_change_for_all() {
		global $user_ID, $wpdb;

		if (!current_user_can($this->capability_required)) {
			// Translation already in WP.
			return __('You do not have sufficient permissions to access this page.');
		}

		if (empty($user_ID)) {
			###$this->log("force_change_for_all() user_ID not set.");
			###$this->log(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS));
			return __("\$user_ID variable not set. Another plugin is misbehaving.", 'login-security-solution');
		}

		$sql = "INSERT INTO `$wpdb->usermeta`
				(user_id, meta_key, meta_value)
				SELECT ID, %s, 1
				FROM `$wpdb->users`
				LEFT JOIN `$wpdb->usermeta`
					ON (`$wpdb->usermeta`.user_id
						= `$wpdb->users`.ID
						AND meta_key = %s)
				WHERE meta_value IS NULL
				AND `$wpdb->users`.ID <> %d";

		$sql = $wpdb->prepare($sql, $this->umk_pw_force_change,
				$this->umk_pw_force_change, $user_ID);

		$wpdb->query($sql);
		if ($wpdb->last_error) {
			return $wpdb->last_error;
		}

		return true;
	}

	/**
	 * Gets the indicator of the status of whether the password
	 * change feature has been used after activation
	 *
	 * @return bool  has the password change feature been used?
	 */
	protected function was_pw_force_change_done() {
		return (bool) get_option($this->option_pw_force_change_name, false);
	}
}
