<?php
/**
 * @file
 * A git pre-commit hook that checks for various compass settings.
 */

// The theme as it appears in sites/all/themes.theme here.
$theme = 'cbrd';

// Create a new test object.
$test = new Test($theme);

// Assert a given option, if the .
$test->assertOption('line_comments', 'false');

class Test {
  /**
   * The theme as it appears in sites/all/themes.
   */
  protected $theme;

  /**
   * The entire config.rb read into memory
   */
  protected $config;

  /**
   * Error messages to display.
   */
  protected $messages;

  /**
   * Any errors or false assertions.
   */
  protected $errors;

  /**
   * Constructor for the test.
   */
  public function __construct($theme) {
    $this->theme = $theme;
    $this->getConfig();
  }

  /**
   * Constructor for the test.
   */
  public function __destruct() {
    if ($this->errors) {
      print $this->messages;
      exit(1);
    }
  }

  /**
   * Returns config.rb for a given theme.
   *
   * @return array
   *   The entire config.rb, one line per element.
   *
   * @todo refactor and make turn this into key/val assoc array.
   */
  protected function getConfig() {
    $path = __DIR__ . "/../../sites/all/themes/$this->theme/config.rb";
    $config = file_get_contents($path);
    if (!$config) {
      print "💩  Cannot Read $path, Peace! 💩 \n";
      exit(1);
    }
    $config = explode("\n", $config);
    $this->config = $config;
  }


  /**
   * Get value of option, NULL if option not found.
   */
  protected function getOption($option) {
    foreach ($this->config as $line) {
      $option_value = array();
      preg_match("/\s*$option\s*=(.*)/", $line, $option_value);
      if (isset($option_value[1])) {
        return trim($option_value[1]);
      }
    }
    return NULL;
  }

  /**
   * Checks to see if an option is present in config.rb.
   */
  protected function isPresent($option) {
    foreach ($this->config as $line) {
      $present = preg_match("/^\s*#*.*$option\s*=/", $line);
      if ($present) {
        return TRUE;
      }
    }
    return FALSE;
  }

  /**
   * Checks if an option is commented out.
   */
  protected function isCommented($option) {
    foreach ($this->config as $line) {
      $commented_out = preg_match("/^\s*#+.*$option/", $line);
      if ($commented_out) {
        return TRUE;
      }
    }
    return FALSE;
  }

  /**
   * Asserts that a given config.rb option contains the expected value.
   *
   * @param string $option
   *   The option to test against.
   *
   * @param string $expected
   *   The expcted value for the option to contain.
   */
  public function assertOption($option, $expected) {
    if (!$this->isPresent($option)) {
      $this->messages .= "💩  FAILED 💩 : $option not present in config.rb.\n";
      $this->errors = TRUE;
    }
    elseif ($this->isCommented($option)) {
      $this->messages .= "💩  FAILED 💩 : $option commented out in config.rb\n";
      $this->errors = TRUE;
    }
    else {
      $actual = $this->getOption($option);
      if (!isset($actual) || $actual != $expected) {
        $message = "💩  FAILED 💩 : config.rb expects $option = $expected, actually $actual\n";
        $this->messages .= $message;
        $this->errors = TRUE;
      }
    }
  }
}
