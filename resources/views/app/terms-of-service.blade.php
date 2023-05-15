@extends('layouts.app', ['fullWidth' => true])

<x-ark-metadata page="terms-of-service" />

@section('title')
    @lang('metatags.terms-of-service.title')
@endsection

@section('content')

    <x-ark-pages-includes-header :title="trans('pages.terms-of-service.title')" />

    <x-ark-documentation>
        @markdown
        *Last updated: 15th of February 2022*

        ***Please read these Terms of Service carefully before using Nodem.***

        Nodem is licensed to You (End-User) by ARK Ecosystem SCIC, located at 12 Place Dauphine, 75001 Paris, France (hereinafter: Licensor), for use only under the terms of this License Agreement.

        By installing Nodem, the user understands that they are using a self-hosted product. The user shall be responsible for the hosting and operation of their own system.

        All rights not expressly granted to You are reserved.

        ## The Application

        Nodem (hereinafter: Application) is a piece of software created to facilitate the management of blockchain nodes and relays. It is used to monitor blockchain nodes and relays from a single user interface.

        ## Scope of License

        - Violations of the obligations mentioned above, as well as the attempt of such infringement, may be subject to prosecution and damages.
        - The Licensor reserves the right to modify the terms and conditions of license.
        - Nothing in this license should be interpreted to restrict third-party terms. When using the Application, You must ensure that You comply with applicable third-party terms and conditions.

        ##  No Maintenance or Support

        ARK Ecosystem SCIC is not obligated, expressed or implied, to provide any maintenance, technical or other support for the Application.

        ## User Generated Contributions

        The Application does not offer users to submit or post content.

        ***Any use of the Application in violation of the foregoing violates these Terms of Use and may result in, among other things, termination or suspension of your rights to use the Application.***

        ## Liability

        Licensor takes no accountability or responsibility for any damages caused due to a breach of duties according to Section 2 of this Agreement. To avoid data loss, You are required to make use of backup functions of the Application to the extent allowed by applicable third-party terms and conditions of use. You are aware that in case of alterations or manipulations of the Application, You will not have access to a licensed Application.

        ## Warranty

        - Licensor warrants that the Application is free of spyware, trojan horses, viruses, or any other malware at the time of Your download. Licensor warrants that the Application works as described in the user documentation.
        - No warranty is provided for the Application that is not executable on the device, that has been unauthorizedly modified, handled inappropriately or culpably, combined or installed with inappropriate hardware or software, used with inappropriate accessories, regardless if by Yourself or by third parties, or if there are any other reasons outside of ARK Ecosystem SCIC's sphere of influence that affect the executability of the Application.

        ## Termination

        The license is valid until terminated by ARK Ecosystem SCIC or by You. Your rights under this license will terminate automatically and without notice from ARK Ecosystem SCIC if You fail to adhere to any term(s) of this license. Upon License termination, You shall stop all use of the Application, and destroy all copies, full or partial, of the Application.

        ## Third-Party Terms of Agreements and Beneficiary

        ARK Ecosystem SCIC represents and warrants that ARK Ecosystem SCIC will comply with applicable third-party terms of agreement when using licensed Application.

        ## Applicable Law

        This license agreement is governed by the laws of France excluding its conflicts of law rules.

        ## Miscellaneous

        If any of the terms of this agreement should be or become invalid, the validity of the remaining provisions shall not be affected. Invalid terms will be replaced by valid ones formulated in a way that will achieve the primary purpose.
        @endmarkdown
    </x-ark-documentation>
@endsection
