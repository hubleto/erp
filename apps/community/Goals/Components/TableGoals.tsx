import React, { Component } from 'react'
import Table, { TableProps, TableState } from 'adios/Table';
import FormGoal, { FormGoalProps, FormGoalState } from './FormGoal';
import { getUrlParam } from 'adios/Helper';
import { FormProps } from 'adios/Form';

interface TableGoalsProps extends TableProps {
  showHeader: boolean,
  showFooter: boolean
}

interface TableGoalsState extends TableState {
}

export default class TableGoals extends Table<TableGoalsProps, TableGoalsState> {
  static defaultProps = {
    ...Table.defaultProps,
    itemsPerPage: 15,
    orderBy: {
      field: "id",
      direction: "desc"
    },
    formUseModalSimple: true,
    model: 'HubletoApp/Community/Goals/Models/Goal',
  }

  props: TableGoalsProps;

  translationContext: string = 'HubletoApp\\Community\\Goals\\Loader::Components\\TableGoals';

  getFormModalProps() {
    if (getUrlParam('recordId') > 0) {
      return {
        ...super.getFormModalProps(),
        type: 'right wide'
      }
    } else return {...super.getFormModalProps()}
  }

  renderForm(): JSX.Element {
    let formProps: FormProps = this.getFormProps();
    return <FormGoal {...formProps}/>;
  }
}