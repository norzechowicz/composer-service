<?php

namespace spec\Ayaline\Bundle\ComposerBundle\Pusher;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;

class ChannelAuthenticatorSpec extends ObjectBehavior
{
    function let(
        RequestStack $requestStack,
        Request $request,
        Session $session,
        ParameterBag $cookies
    ) {
        $requestStack->getCurrentRequest()->willReturn($request);
        $request->getSession()->willReturn($session);
        $request->cookies = $cookies;
        $this->beConstructedWith($requestStack);
    }

    function it_is_channel_authenticator()
    {
        $this->shouldBeAnInstanceOf(
            'Lopi\Bundle\PusherBundle\Authenticator\ChannelAuthenticatorInterface'
        );
    }

    function it_authenticate_channel(Session $session, ParameterBag $cookies)
    {
        $session->getName()->shouldBeCalled()->willReturn('session');
        $cookies->get('session')->shouldBeCalled()->willReturn('new_channel');

        $session->set('socketId', 1)->shouldBeCalled();
        $session->set('channelName', 'new_channel')->shouldBeCalled();

        $this->authenticate(1, 'new_channel')->shouldReturn(true);
    }

    function it_do_not_authenticate_channel_when_cookie_name_is_missing(
        Session $session,
        ParameterBag $cookies
    ) {
        $session->getName()->shouldBeCalled()->willReturn('session');
        $cookies->get('session')->shouldBeCalled()->willReturn('invalid_channel');

        $session->set('socketId', 1)->shouldNotBeCalled();
        $session->set('channelName', 'new_channel')->shouldNotBeCalled();


        $this->authenticate(1, 'new_channel')->shouldReturn(false);
    }
}
