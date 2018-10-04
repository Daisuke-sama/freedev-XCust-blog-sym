<?php
/**
 * Created by Pavel Burylichau
 * Company: EPAM Systems
 * User: pavel_burylichau@epam.com
 * Date: 10/5/18
 * Time: 12:31 AM
 */


namespace App;


use HWI\Bundle\OAuthBundle\OAuth\ResourceOwner\GenericOAuth2ResourceOwner;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Auth0ResourceOwner extends GenericOAuth2ResourceOwner
{
    /**
     * {@inheritdoc}
     */
    protected $paths = [
        'identifier'     => 'user_id',
        'nickname'       => 'nickname',
        'realname'       => 'name',
        'email'          => 'email',
        'profilepicture' => 'picture',
    ];

    /**
     * {@inheritdoc}
     */
    public function getAuthorizationUrl($redirectUri, array $extraParameters = array())
    {
        $params = array_merge($extraParameters, ['audience' => $this->options['audience']]);

        return parent::getAuthorizationUrl($redirectUri, $params);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults(
            [
                'authorization_url' => '{base_url}/authorize',
                'access_token_url'  => '{base_url}/oauth/token',
                'infos_url'         => '{base_url}/userinfo',
                'audience'          => '{base_url}/userinfo',
            ]
        );

        $resolver->setRequired(['{base_url}']);

        $resorter = function (Options $options, $value) {
            return str_replace('{base_url}', $options['base_url'], $value);
        };

        $resolver->setNormalizer('authorization_url', $resorter);
        $resolver->setNormalizer('access_token_url', $resorter);
        $resolver->setNormalizer('infos_url', $resorter);
        $resolver->setNormalizer('audience', $resorter);
    }
}