<?php

declare(strict_types=1);

/**
 * @copyright 2019 Christoph Wurst <christoph@winzerhof-wurst.at>
 *
 * @author 2019 Christoph Wurst <christoph@winzerhof-wurst.at>
 *
 * @license GNU AGPL version 3 or any later version
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace OCA\Mail\Migration;

use OCA\Mail\Service\Provisioning\Manager as ProvisioningManager;
use OCP\Migration\IOutput;
use OCP\Migration\IRepairStep;

class ProvisionAccounts implements IRepairStep {

	/** @var ProvisioningManager */
	private $provisioningManager;

	public function __construct(ProvisioningManager $provisioningManager) {
		$this->provisioningManager = $provisioningManager;
	}

	public function getName(): string {
		return 'Create or update provisioned Mail accounts';
	}

	public function run(IOutput $output) {
		$config = $this->provisioningManager->getConfig();
		if ($config === null) {
			$output->info("No Mail provisioning config set");
			return;
		}

		$cnt = $this->provisioningManager->provision($config);
		$output->info("$cnt accounts provisioned");
	}

}
