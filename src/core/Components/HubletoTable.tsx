import React, { Component } from 'react'
import Table, { TableProps, TableState } from 'adios/Table';
import HubletoForm, { HubletoFormProps, HubletoFormState } from './HubletoForm';
import { getUrlParam } from 'adios/Helper';

interface HubletoTableProps extends TableProps {
  showHeader: boolean,
  showFooter: boolean
}

interface HubletoTableState extends TableState {
}

export default class HubletoTable extends Table<HubletoTableProps, HubletoTableState> {
  static defaultProps = {
    ...Table.defaultProps,
    formUseModalSimple: true,
  }

  props: HubletoTableProps;

  getFormModalProps() {
    if (getUrlParam('recordId') > 0) {
      return {
        ...super.getFormModalProps(),
        type: 'right'
      }
    } else return {...super.getFormModalProps()}
  }

  renderForm(): JSX.Element {
    let formProps: HubletoFormProps = this.getFormProps();
    return <HubletoForm {...formProps}/>;
  }
}