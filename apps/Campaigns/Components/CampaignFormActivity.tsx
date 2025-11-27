import React, { Component } from 'react';
import HubletoForm, { HubletoFormProps, HubletoFormState } from '@hubleto/react-ui/ext/HubletoForm';
import FormActivity, { FormActivityProps, FormActivityState } from '@hubleto/apps/Calendar/Components/FormActivity'
import FormInput from '@hubleto/react-ui/core/FormInput';
import Lookup from '@hubleto/react-ui/core/Inputs/Lookup';

export interface CampaignFormActivityProps extends FormActivityProps {
  idCampaign: number,
}

export interface CampaignFormActivityState extends FormActivityState {
}

export default class CampaignFormActivity<P, S> extends FormActivity<CampaignFormActivityProps, CampaignFormActivityState> {
  static defaultProps: any = {
    ...HubletoForm.defaultProps,
    model: 'Hubleto/App/Community/Campaigns/Models/CampaignActivity',
  };

  props: CampaignFormActivityProps;
  state: CampaignFormActivityState;

  translationContext: string = 'Hubleto\\App\\Community\\Campaigns\\Loader';
  translationContextInner: string = 'Components\\FormActivity';

  constructor(props: CampaignFormActivityProps) {
    super(props);
  }

  getActivitySourceReadable(): string
  {
    return this.translate('Campaign');
  }

  renderCustomInputs(): JSX.Element {
    const R = this.state.record;

    return <>
      {this.inputWrapper('id_campaign', {readonly: R.id > 0})}
    </>;
  }
}
