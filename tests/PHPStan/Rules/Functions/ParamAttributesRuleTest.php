<?php declare(strict_types = 1);

namespace PHPStan\Rules\Functions;

use PHPStan\Php\PhpVersion;
use PHPStan\Rules\AttributesCheck;
use PHPStan\Rules\ClassCaseSensitivityCheck;
use PHPStan\Rules\FunctionCallParametersCheck;
use PHPStan\Rules\NullsafeCheck;
use PHPStan\Rules\PhpDoc\UnresolvableTypeHelper;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleLevelHelper;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<ParamAttributesRule>
 */
class ParamAttributesRuleTest extends RuleTestCase
{

	protected function getRule(): Rule
	{
		$reflectionProvider = $this->createReflectionProvider();
		return new ParamAttributesRule(
			new AttributesCheck(
				$reflectionProvider,
				new FunctionCallParametersCheck(
					new RuleLevelHelper($reflectionProvider, true, false, true, false),
					new NullsafeCheck(),
					new PhpVersion(80000),
					new UnresolvableTypeHelper(),
					true,
					true,
					true,
					true
				),
				new ClassCaseSensitivityCheck($reflectionProvider, false)
			)
		);
	}

	public function testRule(): void
	{
		if (!self::$useStaticReflectionProvider && PHP_VERSION_ID < 80000) {
			$this->markTestSkipped('Test requires PHP 8.0.');
		}

		if (PHP_VERSION_ID < 70200) {
			$this->markTestSkipped('Test requires PHP 7.2.');
		}

		$this->analyse([__DIR__ . '/data/param-attributes.php'], [
			[
				'Attribute class ParamAttributes\Foo does not have the parameter target.',
				33,
			],
			[
				'Attribute class ParamAttributes\Foo does not have the parameter or property target.',
				72,
			],
			[
				'Attribute class ParamAttributes\Qux does not have the parameter target.',
				82,
			],
		]);
	}

}
