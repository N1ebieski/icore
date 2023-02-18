<?php

/**
 * NOTICE OF LICENSE
 *
 * This source file is licenced under the Software License Agreement
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://intelekt.net.pl/pages/regulamin
 *
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.
 *
 * @author    Mariusz Wysokiński <kontakt@intelekt.net.pl>
 * @copyright Since 2019 INTELEKT - Usługi Komputerowe Mariusz Wysokiński
 * @license   https://intelekt.net.pl/pages/regulamin
 */

namespace N1ebieski\ICore\Http\Clients\GeoIP\Responses;

use Torann\GeoIP\Location;
use DivineOmega\Countries\Country;
use DivineOmega\Countries\Countries;
use N1ebieski\ICore\Http\Clients\Response;
use WhiteCube\Lingua\Service as LinguaConverter;

class LocationResponse extends Response
{
    /**
     *
     * @param Location $response
     * @param Countries $countries
     * @param LinguaConverter $converter
     * @return void
     */
    public function __construct(
        protected Location $response,
        protected Countries $countries,
        protected LinguaConverter $converter
    ) {
        parent::__construct($response->toArray());
    }

    /**
     *
     * @return null|string
     */
    public function getLanguage(): ?string
    {
        /** @var Country */
        $country = $this->countries->getByIsoCode($this->get('iso_code'));

        if ($country instanceof Country && isset($country->languageCodes[0])) {
            $language = $this->converter->create($country->languageCodes[0]);

            return $language->toISO_639_1();
        }

        return null;
    }
}
