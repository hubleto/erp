import React, { Component } from 'react';
import FormInput from '@hubleto/react-ui/core/FormInput';
import Lookup from '@hubleto/react-ui/core/Inputs/Lookup';
import FormActivity, { FormActivityProps, FormActivityState } from '@hubleto/apps/Calendar/Components/FormActivity'

export interface OrderFormActivityProps extends FormActivityProps {
  idOrder: number,
  idCustomer?: number,
}

export interface OrderFormActivityState extends FormActivityState {
}

export default class OrderFormActivity<P, S> extends FormActivity<OrderFormActivityProps, OrderFormActivityState> {
  static defaultProps: any = {
    ...FormActivity.defaultProps,
    model: 'Hubleto/App/Community/Orders/Models/OrderActivity',
  };

  props: OrderFormActivityProps;
  state: OrderFormActivityState;

  translationContext: string = 'Hubleto\\App\\Community\\Orders\\Loader';
  translationContextInner: string = 'Components\\FormActivity';

  getActivitySourceReadable(): string
  {
    return this.translate('Order');
  }

  renderCustomInputs(): JSX.Element {
    const R = this.state.record;

    return <>
      {this.inputWrapper('id_order')}
    </>;
  }
}
