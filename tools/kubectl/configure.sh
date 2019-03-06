#!/bin/bash

set -e

kubectl config set-credentials admin/"$K8S_CLUSTER" --username="$K8S_USERNAME" --password="$K8S_PASSWORD" && \
kubectl config set-cluster "$K8S_CLUSTER" --insecure-skip-tls-verify=true --server=$K8S_CLUSTER_API && \
kubectl config set-context default/"$K8S_CLUSTER"/admin --user=admin/"$K8S_CLUSTER" --namespace=default --cluster="$K8S_CLUSTER" && \
kubectl config use-context default/"$K8S_CLUSTER"/admin