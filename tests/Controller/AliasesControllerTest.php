<?php

/**
 * @author Tahaa Karim <tahaalibra@gmail.com>
 *
 * Mail
 *
 * This code is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License, version 3,
 * as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License, version 3,
 * along with this program.  If not, see <http://www.gnu.org/licenses/>
 *
 */

namespace OCA\Mail\Tests\Controller;

use ChristophWurst\Nextcloud\Testing\TestCase;
use OCA\Mail\Controller\AliasesController;
use OCA\Mail\Db\Alias;
use OCP\AppFramework\Http;
use OCP\AppFramework\Http\JSONResponse;

class AliasesControllerTest extends TestCase {
	private $controller;
	private $appName = 'mail';
	private $request;
	private $aliasService;
	private $userId = 'user12345';
	private $userSession;
	private $user;
	private $alias;

	public function setUp() {
		parent::setUp();
		$this->request = $this->getMockBuilder('OCP\IRequest')
			->getMock();
		$this->aliasService = $this->getMockBuilder('OCA\Mail\Service\AliasesService')
			->disableOriginalConstructor()
			->getMock();
		$this->userSession = $this->getMockBuilder('OCP\IUserSession')
			->getMock();
		$this->user = $this->getMockBuilder('OCP\IUser')
			->getMock();
		$this->alias = $this->getMockBuilder('\OCA\Mail\Db\Alias')
			->disableOriginalConstructor()
			->getMock();
		$this->userSession->expects($this->once())
			->method('getUser')
			->will($this->returnValue($this->user));

		$this->controller = new AliasesController($this->appName, $this->request, $this->aliasService, $this->userSession);
	}

	public function testIndex() {
		$accountId = 123;
		$this->user->expects($this->once())
			->method('getUID')
			->will($this->returnValue($this->userId));
		$this->aliasService->expects($this->once())
			->method('findAll')
			->with($accountId, $this->userId)
			->will($this->returnValue([$this->alias]));

		$response = $this->controller->index($accountId);

		$expectedResponse = new JSONResponse([
			$this->alias
		]);
		$this->assertEquals($expectedResponse, $response);
	}

	public function testDestroy() {
		$aliasId = 123;
		$alias = $this->createMock(Alias::class);
		$this->user->expects($this->once())
			->method('getUID')
			->will($this->returnValue($this->userId));
		$this->aliasService->expects($this->once())
			->method('delete')
			->with($this->equalTo($aliasId), $this->equalTo($this->userId))
			->will($this->returnValue($alias));

		$response = $this->controller->destroy($aliasId);

		$expectedResponse = new JSONResponse($alias);
		$this->assertEquals($expectedResponse, $response);
	}

	public function testCreate() {
		$accountId = 123;
		$alias = "alias@marvel.com";
		$aliasName = "Peter Parker";
		$this->aliasService->expects($this->once())
			->method('create')
			->with($this->equalTo($accountId), $this->equalTo($alias), $this->equalTo($aliasName))
			->will($this->returnValue([
				'accountId' => $accountId,
				'name' => $aliasName,
				'alias' => $alias,
				'id' => 123
			]));

		$response = $this->controller->create($accountId, $alias, $aliasName);

		$expected = new JSONResponse([
			'accountId' => $accountId,
			'name' => $aliasName,
			'alias' => $alias,
			'id' => 123
		], Http::STATUS_CREATED);
		$this->assertEquals($expected, $response);
	}
}
