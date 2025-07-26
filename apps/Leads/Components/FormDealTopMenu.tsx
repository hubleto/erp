import React, { Component } from 'react';
import FormDeal, { FormDealProps, FormDealState } from '@hubleto/apps/Deals/Components/FormDeal'
import TranslatedComponent from "@hubleto/react-ui/core/TranslatedComponent";
import request from '@hubleto/react-ui/core/Request';

interface P {
  form: FormDeal<FormDealProps, FormDealState>
}

interface S {
  showDeals: boolean;
}

export default class FormDealTopMenu extends TranslatedComponent<P, S> {
  props: P;
  state: S;

  translationContext: string = 'HubletoApp\\Community\\Deals\\Loader::Components\\FormDeal';

  constructor(props: P) {
    super(props);
    this.state = { showDeals: false };
  }

  render() {
    const form = this.props.form;
    const R = form.state.record;

    return (R.id_lead != null ?
      <a className='btn btn-transparent' href={`${globalThis.main.config.rootUrl}/leads/${R.id_lead}`}>
        <span className='icon'><i className='fas fa-arrow-up-right-from-square'></i></span>
        <span className='text'>{this.translate('Go to original lead')}</span>
      </a>
    : null)
  }
}

