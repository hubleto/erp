import React, { Component } from 'react';
import FormExtended, { FormExtendedProps, FormExtendedState } from '@hubleto/react-ui/ext/FormExtended';

export interface FormProductSupplierProps extends FormExtendedProps {}
export interface FormProductSupplierState extends FormExtendedState {}

export default class FormProductSupplier<P, S> extends FormExtended<FormProductSupplierProps,FormProductSupplierState> {
  static defaultProps: any = {
    ...FormExtended.defaultProps,
    model: 'Hubleto/App/Community/Products/Models/ProductSupplier',
  };

  props: FormProductSupplierProps;
  state: FormProductSupplierState;

  translationContext: string = 'Hubleto\\App\\Community\\Products\\Loader';
  translationContextInner: string = 'Components\\FormProductSupplier';

  constructor(props: FormProductSupplierProps) {
    super(props);
    this.state = {
      ...this.getStateFromProps(props)
    };
  }

  getStateFromProps(props: FormProductSupplierProps) {
    return {
      ...super.getStateFromProps(props),
    };
  }

}