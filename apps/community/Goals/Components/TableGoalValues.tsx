import React, { Component } from 'react'
import Table, { TableProps, TableState } from 'adios/Table';
import { getUrlParam } from 'adios/Helper';
import { FormProps } from 'adios/Form';

interface TableGoalValuesProps extends TableProps {

}

interface TableGoalValuesState extends TableState {
}

export default class TableGoalValues extends Table<TableGoalValuesProps, TableGoalValuesState> {
  static defaultProps = {
    ...Table.defaultProps,
    itemsPerPage: 15,
    orderBy: {
      field: "id",
      direction: "desc"
    },
    formUseModalSimple: true,
    model: 'HubletoApp/Community/Goals/Models/GoalValue',
  }

  props: TableGoalValuesProps;

  translationContext: string = 'HubletoApp\\Community\\Goals\\Loader::Components\\TableGoalValues';
}