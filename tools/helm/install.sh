#!/bin/bash

set -e

curl https://raw.githubusercontent.com/kubernetes/helm/master/scripts/get > get_helm.sh && chmod 700 get_helm.sh && \
sudo ./get_helm.sh && sudo rm -fr ./get_helm.sh && \
helm init --client-only