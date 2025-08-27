import React, { Component } from 'react';
import HubletoForm, { HubletoFormProps, HubletoFormState } from '@hubleto/react-ui/ext/HubletoForm';

export interface FormProductSupplierProps extends HubletoFormProps {}
export interface FormProductSupplierState extends HubletoFormState {}

export default class FormProductSupplier<P, S> extends HubletoForm<FormProductSupplierProps,FormProductSupplierState> {
  static defaultProps: any = {
    ...HubletoForm.defaultProps,
    model: 'HubletoApp/Community/Products/Models/ProductSupplier',
  };

  props: FormProductSupplierProps;
  state: FormProductSupplierState;

  translationContext: string = 'HubletoApp\\Community\\Products\\Loader::Components\\FormProductSupplier';

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