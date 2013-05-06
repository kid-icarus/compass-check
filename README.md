This is a tiny simple little script to check if a config.rb has certain options
set. For now this is extremely simple, and hardcoded to a supplied theme
directory name in a single-site Drupal installation (I'll probably change that
eventually).

## Installation
1. Clone this repo somewhere.
2. `cp /path/to/compass-check/compass-check.php /path/to/project/.git/hooks`
3. For a given project, edit .git/hooks/pre-commit, if this is blank, add
   something along this:

   ```bash
   #!/bin/sh
   /usr/bin/env php compass-check.php
   ```
4. Make sure the pre-commit file is chmod 0775 and owned/grouped appropriately.

## Usage
Usage is included in the script, just look at the first few lines.
